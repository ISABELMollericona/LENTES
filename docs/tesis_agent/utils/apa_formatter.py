from datetime import datetime


class APAFormatter:
    @staticmethod
    def format_heading(text: str, level: int = 1) -> str:
        if level == 1:
            return f"\n\n{text.upper()}\n{'=' * len(text)}\n"
        elif level == 2:
            return f"\n\n{text}\n{'-' * len(text)}\n"
        elif level == 3:
            return f"\n\n{text}\n"
        return f"\n\n{text}\n"

    @staticmethod
    def format_citation(author: str, year: str, page: str = "") -> str:
        if page:
            return f"({author}, {year}, p. {page})"
        return f"({author}, {year})"

    @staticmethod
    def format_parenthetical_citation(author: str, year: str) -> str:
        return f"({author}, {year})"

    @staticmethod
    def format_narrative_citation(author: str, year: str) -> str:
        return f"{author} ({year})"

    @staticmethod
    def format_reference(author: str, year: str, title: str,
                          publisher: str = "", doi: str = "", url: str = "",
                          edition: str = "", is_book: bool = True) -> str:
        if is_book:
            base = f"{author} ({year}). *{title}*"
            if edition:
                base += f" ({edition} ed.)"
            if doi:
                base += f". {publisher}. https://doi.org/{doi}"
            else:
                base += f". {publisher}"
            return base
        else:
            journal, volume, pages = publisher, "", ""
            base = f"{author} ({year}). {title}."
            base += f" *{journal}*"
            if volume:
                base += f", *{volume}*"
            if pages:
                base += f", {pages}"
            if doi:
                base += f". https://doi.org/{doi}"
            elif url:
                base += f". {url}"
            return base

    @staticmethod
    def format_in_text_citation(concept: str, author: str, year: str) -> str:
        return f"En cuanto a {concept}, {APAFormatter.format_narrative_citation(author, year)}"

    @staticmethod
    def generate_sample_references(topic: str, count: int = 5) -> list[str]:
        samples = {
            "ecommerce": [
                "Chaffey, D. (2022). *Digital marketing: Strategy, implementation and practice* (8th ed.). Pearson.",
                "Laudon, K. C., & Traver, C. G. (2023). *E-commerce: Business, technology, society* (17th ed.). Pearson.",
                "Turban, E., Outland, J., King, D., Lee, J. K., Liang, T. P., & Turban, D. C. (2021). *Electronic commerce: A managerial and social networks perspective* (9th ed.). Springer.",
            ],
            "inteligencia_artificial": [
                "Russell, S., & Norvig, P. (2021). *Artificial intelligence: A modern approach* (4th ed.). Pearson.",
                "Goodfellow, I., Bengio, Y., & Courville, A. (2016). *Deep learning*. MIT Press.",
                "Chollet, F. (2021). *Deep learning with Python* (2nd ed.). Manning.",
            ],
            "laravel": [
                "Stauffer, M. (2023). *Laravel: Up and running: A framework for building modern PHP apps* (3rd ed.). O'Reilly Media.",
                "Voukatas, A. (2022). *Practical Laravel: Build simple to complex web applications*. Apress.",
            ]
        }

        refs = []
        for key, references in samples.items():
            if key.lower() in topic.lower():
                refs.extend(references)

        if len(refs) < count:
            refs.append(
                f"Autores corporativos. ({datetime.now().year}). *{topic}*. Editorial Académica."
            )

        return refs[:count]

    @staticmethod
    def apply_apa_rules(text: str) -> str:
        text = text.replace("'", "''")
        text = text.replace("...", "…")
        text = text.replace(" -- ", " – ")
        return text
