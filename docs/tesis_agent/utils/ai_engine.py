import json
import time
import logging
from typing import Optional, Callable
from config import Config


class AIProviderError(Exception):
    pass


class AIEngine:
    """Motor multi-proveedor con fallback automático entre OpenAI, Gemini y Groq."""

    def __init__(self):
        self.providers = self._init_providers()
        self.current_provider = Config.AI_PRIMARY_PROVIDER
        self.stats = {p: {"calls": 0, "errors": 0, "total_tokens": 0} for p in self.providers}

    def _init_providers(self) -> dict:
        providers = {}

        if Config.OPENAI_API_KEY and Config.OPENAI_API_KEY != "your_openai_api_key_here":
            try:
                from openai import OpenAI
                providers["openai"] = {
                    "client": OpenAI(api_key=Config.OPENAI_API_KEY),
                    "model": Config.OPENAI_MODEL,
                    "priority": 0,
                }
            except Exception:
                pass

        if Config.GEMINI_API_KEY and Config.GEMINI_API_KEY != "your_gemini_api_key_here":
            try:
                from google import genai
                providers["gemini"] = {
                    "client": genai.Client(api_key=Config.GEMINI_API_KEY),
                    "model": Config.GEMINI_MODEL,
                    "priority": 1,
                }
            except Exception:
                pass

        if Config.GROQ_API_KEY and Config.GROQ_API_KEY != "your_groq_api_key_here":
            try:
                from groq import Groq
                providers["groq"] = {
                    "client": Groq(api_key=Config.GROQ_API_KEY),
                    "model": Config.GROQ_MODEL,
                    "priority": 2,
                }
            except Exception:
                pass

        self._apply_fallback_order(providers)
        return providers

    def _apply_fallback_order(self, providers: dict):
        order = [Config.AI_PRIMARY_PROVIDER]
        fallbacks = [p.strip() for p in Config.AI_FALLBACK_PROVIDERS.split(",") if p.strip()]
        order.extend(f for f in fallbacks if f not in order)

        for i, name in enumerate(order):
            if name in providers:
                providers[name]["priority"] = i

    def is_available(self) -> bool:
        return len(self.providers) > 0

    def get_available_providers(self) -> list[str]:
        return sorted(self.providers.keys(), key=lambda p: self.providers[p]["priority"])

    def generate(self, prompt: str, system_prompt: str = "",
                 temperature: float = None, max_tokens: int = None,
                 response_format: str = None) -> tuple[str, str, dict]:
        temp = temperature if temperature is not None else Config.TEMPERATURE
        tokens = max_tokens if max_tokens is not None else Config.MAX_TOKENS

        last_error = None
        ordered = sorted(self.providers.items(), key=lambda x: x[1]["priority"])

        for name, provider in ordered:
            try:
                self.stats[name]["calls"] += 1
                self.current_provider = name

                if name == "openai":
                    result = self._call_openai(provider, prompt, system_prompt, temp, tokens, response_format)
                elif name == "gemini":
                    result = self._call_gemini(provider, prompt, system_prompt, temp, tokens, response_format)
                elif name == "groq":
                    result = self._call_groq(provider, prompt, system_prompt, temp, tokens, response_format)
                else:
                    continue

                text, model_used, usage = result
                self.stats[name]["total_tokens"] += usage.get("total_tokens", 0)
                return text, model_used, usage

            except Exception as e:
                self.stats[name]["errors"] += 1
                last_error = e
                print(f"  ⚠ {name} falló: {e}. Probando siguiente proveedor...")

        raise AIProviderError(
            f"Todos los proveedores fallaron. Último error: {last_error}"
        )

    def _call_openai(self, provider: dict, prompt: str, system_prompt: str,
                      temperature: float, max_tokens: int, response_format: str) -> tuple:
        client = provider["client"]
        model = provider["model"]
        messages = []

        if system_prompt:
            messages.append({"role": "system", "content": system_prompt})
        messages.append({"role": "user", "content": prompt})

        kwargs = {
            "model": model,
            "messages": messages,
            "temperature": temperature,
            "max_tokens": max_tokens,
        }

        if response_format == "json":
            from openai import NotGiven
            kwargs["response_format"] = {"type": "json_object"}

        response = client.chat.completions.create(**kwargs)
        text = response.choices[0].message.content.strip()

        usage = {
            "prompt_tokens": response.usage.prompt_tokens if response.usage else 0,
            "completion_tokens": response.usage.completion_tokens if response.usage else 0,
            "total_tokens": response.usage.total_tokens if response.usage else 0,
        }

        return text, model, usage

    def _call_gemini(self, provider: dict, prompt: str, system_prompt: str,
                      temperature: float, max_tokens: int, response_format: str) -> tuple:
        client = provider["client"]
        model = provider["model"]

        contents = [prompt]
        if system_prompt:
            contents.insert(0, system_prompt)

        response = client.models.generate_content(
            model=model,
            contents=contents,
            config={
                "temperature": temperature,
                "max_output_tokens": max_tokens,
            }
        )

        text = response.text.strip()

        usage = {
            "prompt_tokens": 0,
            "completion_tokens": 0,
            "total_tokens": 0,
        }

        return text, model, usage

    def _call_groq(self, provider: dict, prompt: str, system_prompt: str,
                    temperature: float, max_tokens: int, response_format: str) -> tuple:
        client = provider["client"]
        model = provider["model"]
        messages = []

        if system_prompt:
            messages.append({"role": "system", "content": system_prompt})
        messages.append({"role": "user", "content": prompt})

        kwargs = {
            "model": model,
            "messages": messages,
            "temperature": temperature,
            "max_tokens": max_tokens,
        }

        if response_format == "json":
            kwargs["response_format"] = {"type": "json_object"}

        response = client.chat.completions.create(**kwargs)
        text = response.choices[0].message.content.strip()

        usage = {
            "prompt_tokens": response.usage.prompt_tokens if response.usage else 0,
            "completion_tokens": response.usage.completion_tokens if response.usage else 0,
            "total_tokens": response.usage.total_tokens if response.usage else 0,
        }

        return text, model, usage

    def generate_with_retry(self, prompt: str, system_prompt: str = "",
                             temperature: float = None, max_tokens: int = None,
                             response_format: str = None,
                             max_retries: int = 3) -> tuple[str, str, dict]:
        last_error = None
        for attempt in range(max_retries):
            try:
                return self.generate(prompt, system_prompt, temperature, max_tokens, response_format)
            except AIProviderError as e:
                last_error = e
                if attempt < max_retries - 1:
                    wait = (attempt + 1) * 2
                    print(f"  ↻ Reintento {attempt + 1}/{max_retries} en {wait}s...")
                    time.sleep(wait)
                else:
                    raise AIProviderError(f"Agotados {max_retries} reintentos. {last_error}")

        raise AIProviderError(str(last_error))

    def get_stats_report(self) -> str:
        lines = ["\n--- Estadísticas del Motor IA ---"]
        for name, data in self.stats.items():
            lines.append(f"  {name}: {data['calls']} llamadas, "
                         f"{data['errors']} errores, "
                         f"{data['total_tokens']} tokens")
        lines.append(f"  Proveedor activo: {self.current_provider}")
        return "\n".join(lines)
