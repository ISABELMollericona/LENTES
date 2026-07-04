import re
from pathlib import Path
from typing import Optional
from google.oauth2.credentials import Credentials
from google.auth.transport.requests import Request
from google_auth_oauthlib.flow import InstalledAppFlow
from googleapiclient.discovery import build
from googleapiclient.errors import HttpError

from config import Config


class GoogleDocsManager:
    SCOPES = [
        "https://www.googleapis.com/auth/documents",
        "https://www.googleapis.com/auth/drive",
        "https://www.googleapis.com/auth/drive.file",
    ]

    def __init__(self):
        self.creds = self._authenticate()
        self.docs_service = build("docs", "v1", credentials=self.creds)
        self.drive_service = build("drive", "v3", credentials=self.creds)
        self.doc_id = Config.GOOGLE_DOCS_TOPIC_ID

    def _authenticate(self):
        creds = None
        token_path = Config.CREDENTIALS_DIR / "token.json"

        if token_path.exists():
            import json
            creds = Credentials.from_authorized_user_file(str(token_path), self.SCOPES)

        if not creds or not creds.valid:
            if creds and creds.expired and creds.refresh_token:
                creds.refresh(Request())
            else:
                creds_path = Config.GOOGLE_CREDENTIALS_FILE
                if not creds_path.exists():
                    raise FileNotFoundError(
                        f"Credenciales de Google no encontradas en {creds_path}. "
                        "Descarga el archivo JSON desde Google Cloud Console."
                    )
                flow = InstalledAppFlow.from_client_secrets_file(
                    str(creds_path), self.SCOPES,
                    redirect_uri="http://localhost:8080/"
                )
                creds = flow.run_local_server(
                    port=8080,
                    open_browser=True,
                    authorization_prompt_message="",
                    success_message="Autenticación exitosa! Puedes cerrar esta ventana."
                )

            token_path.parent.mkdir(parents=True, exist_ok=True)
            import json
            token_path.write_text(creds.to_json(), encoding="utf-8")

        return creds

    def create_document(self, title: str = "") -> str:
        if not title:
            title = Config.THESIS_TITLE
        try:
            doc = self.docs_service.documents().create(body={"title": title}).execute()
            self.doc_id = doc["documentId"]
            self._insert_initial_structure()
            return self.doc_id
        except HttpError as e:
            raise RuntimeError(f"Error creando documento: {e}")

    def _insert_initial_structure(self):
        requests = [
            {"insertText": {"location": {"index": 1}, "text": f"{Config.THESIS_TITLE}\n\n"}},
            {"updateParagraphStyle": {
                "range": {"startIndex": 1, "endIndex": len(Config.THESIS_TITLE) + 1},
                "paragraphStyle": {"namedStyleType": "TITLE"},
                "fields": "namedStyleType"
            }},
        ]
        for section_key, section_data in Config.SECTIONS.items():
            chapter_title = section_data["title"]
            requests.append({
                "insertText": {"location": {"index": 1}, "text": f"\n{chapter_title}\n"}
            })
            for sub_key, sub_title in section_data["subsections"].items():
                requests.append({
                    "insertText": {"location": {"index": 1}, "text": f"\n{sub_key} {sub_title}\n"}
                })

        requests.reverse()
        self._batch_update(requests)

    def read_document(self) -> str:
        if not self.doc_id:
            raise ValueError("No hay un documento abierto. Usa create_document o set_document_id.")
        try:
            doc = self.docs_service.documents().get(documentId=self.doc_id).execute()
            return self._extract_text(doc)
        except HttpError as e:
            raise RuntimeError(f"Error leyendo documento: {e}")

    def _extract_text(self, doc: dict) -> str:
        text = ""
        if "body" in doc and "content" in doc["body"]:
            for item in doc["body"]["content"]:
                if "paragraph" in item:
                    for elem in item["paragraph"]["elements"]:
                        if "textRun" in elem:
                            text += elem["textRun"].get("content", "")
                elif "table" in item:
                    for row in item["table"]["tableRows"]:
                        for cell in row["tableCells"]:
                            for content in cell["content"]:
                                if "paragraph" in content:
                                    for elem in content["paragraph"]["elements"]:
                                        if "textRun" in elem:
                                            text += elem["textRun"].get("content", "")
        return text

    def detect_sections(self) -> dict[str, str]:
        content = self.read_document()
        sections = {}
        current_section = "header"

        lines = content.split("\n")
        for line in lines:
            line = line.strip()
            if not line:
                continue
            if re.match(r'^(CAPÍTULO|CAPITULO)\s+\w+', line, re.IGNORECASE):
                current_section = line
                sections[current_section] = ""
            elif re.match(r'^\d+\.\d+', line):
                current_section = line
                sections[current_section] = ""
            elif current_section not in ("header",):
                sections[current_section] = sections.get(current_section, "") + line + "\n"

        return sections

    def find_empty_sections(self) -> list[str]:
        sections = self.detect_sections()
        empty = []
        for title, content in sections.items():
            cleaned = content.strip()
            if len(cleaned) < 50:
                empty.append(title)
        return empty

    def insert_content(self, section_title: str, content: str):
        if not self.doc_id:
            raise ValueError("No hay documento abierto.")

        full_content = self.read_document()
        section_pos = full_content.find(section_title)

        if section_pos == -1:
            full_content += f"\n{section_title}\n{content}\n"
            self._replace_all_content(full_content)
            return

        after_section = full_content[section_pos + len(section_title):]
        next_section_match = re.search(r'\n\d+\.\d+\s|\nCAP[TÍI]TULO|\nBIBLIOGRAFÍA|\nANEXOS', after_section)
        insert_point = section_pos + len(section_title)

        text_before = full_content[:insert_point]
        text_after = after_section

        if content.strip():
            new_content = f"{text_before}\n{content}\n{text_after}"
        else:
            new_content = full_content

        self._replace_all_content(new_content)

    def _replace_all_content(self, new_content: str):
        try:
            doc = self.docs_service.documents().get(documentId=self.doc_id).execute()
            end_index = doc["body"]["content"][-1]["endIndex"] - 1

            requests = [
                {"deleteContentRange": {
                    "range": {"startIndex": 1, "endIndex": end_index}
                }},
                {"insertText": {
                    "location": {"index": 1},
                    "text": new_content
                }}
            ]
            self._batch_update(requests)
        except HttpError as e:
            raise RuntimeError(f"Error reemplazando contenido: {e}")

    def _batch_update(self, requests: list):
        try:
            self.docs_service.documents().batchUpdate(
                documentId=self.doc_id,
                body={"requests": requests}
            ).execute()
        except HttpError as e:
            raise RuntimeError(f"Error en batch update: {e}")

    def get_document_url(self) -> str:
        if self.doc_id:
            return f"https://docs.google.com/document/d/{self.doc_id}/edit"
        return ""

    def export_as_docx(self, output_path: Optional[Path] = None):
        if not output_path:
            output_path = Config.OUTPUT_DIR / "tesis_completa.docx"
        output_path.parent.mkdir(parents=True, exist_ok=True)

        try:
            request = self.drive_service.files().export_media(
                fileId=self.doc_id, mimeType="application/vnd.openxmlformats-officedocument.wordprocessingml.document"
            )
            content = request.execute()
            output_path.write_bytes(content)
            return output_path
        except HttpError as e:
            raise RuntimeError(f"Error exportando documento: {e}")

    def update_table_of_contents(self):
        requests = [
            {"insertTableOfContents": {
                "location": {"index": 1},
                "type": "TOC"
            }}
        ]
        self._batch_update(requests)
