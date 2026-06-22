import io
from datetime import datetime

from reportlab.lib.pagesizes import A4
from reportlab.lib.styles import ParagraphStyle, getSampleStyleSheet
from reportlab.lib.units import cm
from reportlab.platypus import (
    Paragraph,
    SimpleDocTemplate,
    Spacer,
    Table,
    TableStyle,
)

ROLE_LABELS = {
    "organizer": "Organizador",
    "attendee": "Participante",
    "optional": "Opcional",
}


def generate_meeting_pdf(
    *,
    company_name: str,
    meeting,
    owner_name: str,
    participants: list[dict],
    subjects: list[dict],
    timeline: list[dict],
) -> io.BytesIO:
    buf = io.BytesIO()
    doc = SimpleDocTemplate(
        buf, pagesize=A4,
        leftMargin=2 * cm, rightMargin=2 * cm,
        topMargin=2 * cm, bottomMargin=2 * cm,
    )
    styles = getSampleStyleSheet()
    title_style = ParagraphStyle(
        "MeetTitle", parent=styles["Heading1"], fontSize=16,
        spaceAfter=6,
    )
    h2 = ParagraphStyle(
        "H2", parent=styles["Heading2"], fontSize=12,
        spaceBefore=12, spaceAfter=6,
    )
    body = styles["BodyText"]

    elements: list = []

    elements.append(Paragraph(company_name, styles["Heading3"]))
    elements.append(Spacer(1, 0.2 * cm))
    elements.append(Paragraph("Ata de Reunião", styles["Heading4"]))
    elements.append(Spacer(1, 0.3 * cm))
    elements.append(Paragraph(meeting.title, title_style))
    elements.append(Spacer(1, 0.3 * cm))

    meta = [
        ["Status", meeting.status or "—"],
        ["Local", meeting.location or "—"],
        ["Responsável", owner_name],
        [
            "Data/Hora",
            meeting.scheduled_at.strftime("%d/%m/%Y %H:%M")
            if meeting.scheduled_at
            else "—",
        ],
        [
            "Criado em",
            meeting.created_at.strftime("%d/%m/%Y %H:%M")
            if meeting.created_at
            else "—",
        ],
    ]
    t = Table(meta, colWidths=[4 * cm, 12 * cm])
    t.setStyle(TableStyle([
        ("FONTNAME", (0, 0), (0, -1), "Helvetica-Bold"),
        ("FONTSIZE", (0, 0), (-1, -1), 10),
        ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
        ("TOPPADDING", (0, 0), (-1, -1), 2),
        ("VALIGN", (0, 0), (-1, -1), "TOP"),
    ]))
    elements.append(t)

    if meeting.description:
        elements.append(Paragraph("Descrição", h2))
        elements.append(Paragraph(meeting.description, body))

    if participants:
        elements.append(Paragraph("Participantes", h2))
        p_data = [["Nome", "Papel"]]
        for p in participants:
            role_label = ROLE_LABELS.get(p.get("role", ""), p.get("role", ""))
            p_data.append([p.get("name", "—"), role_label])
        pt = Table(p_data, colWidths=[10 * cm, 6 * cm])
        pt.setStyle(TableStyle([
            ("FONTNAME", (0, 0), (-1, 0), "Helvetica-Bold"),
            ("FONTSIZE", (0, 0), (-1, -1), 10),
            ("BOTTOMPADDING", (0, 0), (-1, -1), 4),
            ("TOPPADDING", (0, 0), (-1, -1), 2),
            ("LINEBELOW", (0, 0), (-1, 0), 0.5, (0, 0, 0)),
        ]))
        elements.append(pt)

    if subjects:
        elements.append(Paragraph("Pautas", h2))
        for i, s in enumerate(subjects, 1):
            check = "✓" if s.get("resolved") else "○"
            title = s.get("title", "")
            elements.append(
                Paragraph(f"{check} {i}. {title}", body)
            )
            if s.get("description"):
                elements.append(
                    Paragraph(
                        f"&nbsp;&nbsp;&nbsp;&nbsp;{s['description']}",
                        body,
                    )
                )

    if timeline:
        elements.append(Paragraph("Histórico", h2))
        for entry in timeline:
            ts = entry.get("created_at", "")
            if isinstance(ts, datetime):
                ts = ts.strftime("%d/%m/%Y %H:%M")
            user = entry.get("user", entry.get("user_name", "Sistema"))
            etype = entry.get("event_type", "")
            msg = entry.get("message", "")
            if etype == "comment" and msg:
                elements.append(
                    Paragraph(f"<b>{user}</b> ({ts}): {msg}", body)
                )
            elif etype == "create":
                elements.append(
                    Paragraph(
                        f"<b>{user}</b> ({ts}): criou a reunião",
                        body,
                    )
                )
            elif etype == "update":
                diff = entry.get("changes", entry.get("diff", {}))
                if diff:
                    changes = "; ".join(
                        f"{k}: {v}" for k, v in diff.items()
                    )
                    elements.append(
                        Paragraph(
                            f"<b>{user}</b> ({ts}): {changes}", body
                        )
                    )

    doc.build(elements)
    buf.seek(0)
    return buf
