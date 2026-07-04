#!/usr/bin/env python3
"""
TesisAgent - Agente autónomo para redacción de tesis académicas en Google Docs.
Carrera: Ingeniería de Sistemas
Formato: APA 7
Motor: OpenAI GPT-4o + ChromaDB + Google Docs API
"""

import sys
import time
from pathlib import Path
from config import Config
from utils.thesis_generator import ThesisGenerator
from utils.chapter_manager import ChapterManager
from utils.database import DatabaseManager


def print_header():
    print("=" * 60)
    print("   TESIS AGENT v1.0")
    print("   Agente Autónomo de Redacción de Tesis")
    print("   Ingeniería de Sistemas - APA 7")
    print("=" * 60)
    print()


def print_menu():
    print("\nCOMANDOS DISPONIBLES:")
    print("  generate-all       → Generar todas las secciones pendientes")
    print("  generate <id>      → Generar una sección específica (ej: generate 2.3)")
    print("  feed               → Ingresar información desde NotebookLM")
    print("  status             → Mostrar progreso de la tesis")
    print("  stats              → Mostrar estadísticas detalladas")
    print("  resume             → Reanudar desde la última sección pendiente")
    print("  export             → Exportar tesis a archivo de texto")
    print("  google-create      → Crear documento en Google Docs")
    print("  google-update      → Actualizar Google Docs con contenido actual")
    print("  init               → Inicializar/restablecer secciones")
    print("  help               → Mostrar este menú")
    print("  exit               → Salir")
    print()


def cmd_generate_all(agent: ThesisGenerator):
    print("\nGenerando todas las secciones pendientes...")
    agent.generate_all()


def cmd_generate(agent: ThesisGenerator, section_id: str):
    print(f"\nGenerando sección {section_id}...")
    agent.generate_section(section_id)


def cmd_feed(agent: ThesisGenerator):
    print("\n=== INGRESAR INFORMACIÓN DESDE NOTEBOOKLM ===")
    print("Pega el texto copiado desde NotebookLM.")
    print("Escribe 'FIN' en una línea sola para terminar.\n")

    lines = []
    while True:
        line = input()
        if line.strip().upper() == "FIN":
            break
        lines.append(line)

    text = "\n".join(lines)
    if text.strip():
        agent.feed_notebooklm(text)
    else:
        print("No se ingresó texto.")


def cmd_status(agent: ThesisGenerator):
    print("\n" + "=" * 60)
    print("ESTADO DE LA TESIS")
    print("=" * 60)
    print(agent.get_progress_report())


def cmd_stats(agent: ThesisGenerator):
    stats = agent.get_stats()
    print("\n" + "=" * 60)
    print("ESTADÍSTICAS")
    print("=" * 60)
    print(f"  Secciones totales:     {stats['total_sections']}")
    print(f"  Secciones completadas: {stats['completed']}")
    print(f"  Secciones pendientes:  {stats['pending']}")
    print(f"  Palabras totales:      {stats['total_words']:,}")
    print(f"  Referencias totales:   {stats['total_references']}")
    print(f"  Tokens consumidos:     {stats['total_tokens']:,}")
    print(f"  Secciones generadas:   {stats.get('sections_generated', 0)}")
    print(f"  Errores:               {stats.get('errors', 0)}")
    try:
        print(agent.ai_engine.get_stats_report())
    except Exception:
        pass


def cmd_resume(agent: ThesisGenerator):
    print("\nReanudando generación desde la última sección pendiente...")
    agent.generate_all()


def cmd_export(agent: ThesisGenerator):
    from utils.chapter_manager import ChapterManager
    cm = ChapterManager()
    full_thesis = cm.assemble_full_thesis()

    output_dir = Config.OUTPUT_DIR
    output_dir.mkdir(parents=True, exist_ok=True)

    txt_path = output_dir / "tesis_completa.txt"
    txt_path.write_text(full_thesis, encoding="utf-8")
    print(f"\n✓ Tesis exportada a: {txt_path}")
    print(f"  Total: {len(full_thesis.split())} palabras")


def cmd_google_create(agent: ThesisGenerator):
    if not agent.google_docs:
        print("Google Docs no está habilitado.")
        return
    try:
        doc_id = agent.google_docs.create_document()
        url = agent.google_docs.get_document_url()
        print(f"\n✓ Documento creado en Google Docs")
        print(f"  URL: {url}")
        print(f"  ID: {doc_id}")
    except Exception as e:
        print(f"\n✗ Error: {e}")


def cmd_google_update(agent: ThesisGenerator):
    if not agent.google_docs:
        print("Google Docs no está habilitado.")
        return
    try:
        from utils.chapter_manager import ChapterManager
        cm = ChapterManager()
        full_thesis = cm.assemble_full_thesis()
        agent.google_docs._replace_all_content(full_thesis)
        print(f"\n✓ Google Docs actualizado")
    except Exception as e:
        print(f"\n✗ Error: {e}")


def cmd_init(agent: ThesisGenerator):
    confirm = input("¿Restablecer todas las secciones? (s/N): ")
    if confirm.lower() == "s":
        agent.initialize()
        print("✓ Secciones inicializadas.")
    else:
        print("Operación cancelada.")


def main():
    print_header()

    print("Inicializando TesisAgent...")
    agent = ThesisGenerator(use_google_docs=True)

    try:
        agent.initialize()
    except Exception as e:
        print(f"⚠ Advertencia: {e}")
        print("El sistema funcionará sin Google Docs.")
        agent = ThesisGenerator(use_google_docs=False)
        agent.initialize()

    providers = agent.ai_engine.get_available_providers()
    print(f"  Título: {Config.THESIS_TITLE[:60]}...")
    print(f"  Proveedores IA: {', '.join(providers) if providers else 'NINGUNO'}")
    print(f"  Orden: {Config.AI_PRIMARY_PROVIDER} (principal) + {Config.AI_FALLBACK_PROVIDERS} (fallback)")

    print_menu()

    while True:
        try:
            cmd = input("\n> ").strip().lower()
        except (EOFError, KeyboardInterrupt):
            print("\nSaliendo...")
            break

        if not cmd:
            continue

        if cmd == "exit":
            print("Saliendo del agente...")
            break
        elif cmd == "generate-all":
            cmd_generate_all(agent)
        elif cmd.startswith("generate "):
            parts = cmd.split(" ", 1)
            if len(parts) > 1:
                cmd_generate(agent, parts[1])
            else:
                print("Uso: generate <section_id> (ej: generate 2.3)")
        elif cmd == "feed":
            cmd_feed(agent)
        elif cmd == "status":
            cmd_status(agent)
        elif cmd == "stats":
            cmd_stats(agent)
        elif cmd == "resume":
            cmd_resume(agent)
        elif cmd == "export":
            cmd_export(agent)
        elif cmd == "google-create":
            cmd_google_create(agent)
        elif cmd == "google-update":
            cmd_google_update(agent)
        elif cmd == "init":
            cmd_init(agent)
        elif cmd == "help":
            print_menu()
        else:
            print(f"Comando desconocido: {cmd}. Escribe 'help' para ver los comandos.")

    print("\nGracias por usar TesisAgent. ¡Hasta luego!")


if __name__ == "__main__":
    main()
