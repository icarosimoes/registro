"use client";

import { useEffect, useState } from "react";
import { ArrowRightLeft, Check, Eye, Plus, X } from "lucide-react";
import type { HandoffItem } from "@/app/actions";
import {
  fetchHandoffsForReportAction,
  createHandoffAction,
  markHandoffReadAction,
  resolveHandoffAction,
} from "@/app/actions";

const shiftLabels: Record<string, string> = { morning: "Manhã", afternoon: "Tarde", night: "Noite" };
const statusClass: Record<string, string> = {
  pendente: "status status-waiting",
  lido: "status status-progress",
  resolvido: "status status-done",
};

export function HandoffSection({ shiftReportId, shiftDate, shiftType }: {
  shiftReportId: number;
  shiftDate?: string;
  shiftType?: string;
}) {
  const [items, setItems] = useState<HandoffItem[]>([]);
  const [loading, setLoading] = useState(true);
  const [showForm, setShowForm] = useState(false);
  const [saving, setSaving] = useState(false);
  const [feedback, setFeedback] = useState("");

  useEffect(() => {
    fetchHandoffsForReportAction(shiftReportId)
      .then(setItems)
      .finally(() => setLoading(false));
  }, [shiftReportId]);

  function showFeedback(msg: string) {
    setFeedback(msg);
    setTimeout(() => setFeedback(""), 2600);
  }

  async function handleCreate(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setSaving(true);
    const fd = new FormData(e.currentTarget);
    const result = await createHandoffAction({
      title: String(fd.get("title")),
      description: String(fd.get("description") ?? "") || undefined,
      priority: String(fd.get("priority") ?? "media"),
      target_shift: shiftType ?? undefined,
      target_date: shiftDate ?? new Date().toISOString().split("T")[0],
      shift_report_id: shiftReportId,
    });
    setSaving(false);
    if (result.ok) {
      showFeedback("Pendência criada.");
      setShowForm(false);
      fetchHandoffsForReportAction(shiftReportId).then(setItems);
    } else {
      showFeedback(result.error ?? "Erro ao criar.");
    }
  }

  async function handleRead(id: number) {
    const result = await markHandoffReadAction(id);
    if (result.ok) {
      showFeedback("Marcada como lida.");
      fetchHandoffsForReportAction(shiftReportId).then(setItems);
    }
  }

  async function handleResolve(id: number) {
    const result = await resolveHandoffAction(id);
    if (result.ok) {
      showFeedback("Pendência resolvida.");
      fetchHandoffsForReportAction(shiftReportId).then(setItems);
    }
  }

  return (
    <fieldset className="form-section">
      <legend><ArrowRightLeft size={14} /> Pendências do turno</legend>

      {loading ? (
        <p style={{ color: "var(--muted)", fontSize: "var(--font-sm)" }}>Carregando...</p>
      ) : items.length === 0 && !showForm ? (
        <p style={{ color: "var(--muted)", fontSize: "var(--font-sm)" }}>Nenhuma pendência registrada para este turno.</p>
      ) : (
        <div style={{ display: "flex", flexDirection: "column", gap: "var(--sp-2)" }}>
          {items.map((item) => (
            <div key={item.id} style={{
              display: "flex", alignItems: "center", gap: "var(--sp-3)",
              padding: "var(--sp-2) var(--sp-3)",
              background: "var(--field-bg)", borderRadius: "var(--radius-sm)",
              border: "1px solid var(--field-border)",
            }}>
              <div style={{ flex: 1, minWidth: 0 }}>
                <strong style={{ fontSize: "var(--font-base)" }}>{item.title}</strong>
                {item.description && (
                  <p style={{ fontSize: "var(--font-sm)", color: "var(--muted)", margin: "2px 0 0" }}>{item.description}</p>
                )}
                <div style={{ display: "flex", gap: "var(--sp-2)", marginTop: 4, fontSize: "var(--font-xs)", color: "var(--label)" }}>
                  {item.target_shift && <span>{shiftLabels[item.target_shift] ?? item.target_shift}</span>}
                  <span>{item.created_by_name ?? "Sistema"}</span>
                </div>
              </div>
              <span className={statusClass[item.status] ?? "status"}>
                {item.status === "pendente" ? "Pendente" : item.status === "lido" ? "Lido" : "Resolvido"}
              </span>
              {item.status === "pendente" && (
                <button type="button" onClick={() => handleRead(item.id)} title="Marcar como lida"
                  style={{ background: "none", border: "none", cursor: "pointer", color: "var(--blue)", padding: 4 }}>
                  <Eye size={16} />
                </button>
              )}
              {item.status !== "resolvido" && (
                <button type="button" onClick={() => handleResolve(item.id)} title="Resolver"
                  style={{ background: "none", border: "none", cursor: "pointer", color: "var(--green)", padding: 4 }}>
                  <Check size={16} />
                </button>
              )}
            </div>
          ))}
        </div>
      )}

      {showForm ? (
        <form onSubmit={handleCreate} style={{ display: "flex", flexDirection: "column", gap: "var(--sp-3)", marginTop: "var(--sp-3)" }}>
          <label>Título<input name="title" required placeholder="Descreva a pendência..." /></label>
          <label>Descrição<textarea name="description" rows={2} placeholder="Detalhes opcionais..." /></label>
          <div className="form-grid">
            <label>Prioridade
              <select name="priority" defaultValue="media">
                <option value="baixa">Baixa</option>
                <option value="media">Média</option>
                <option value="alta">Alta</option>
                <option value="urgente">Urgente</option>
              </select>
            </label>
          </div>
          <div style={{ display: "flex", gap: "var(--sp-2)" }}>
            <button type="submit" disabled={saving}>{saving ? "Salvando..." : "Criar pendência"}</button>
            <button type="button" onClick={() => setShowForm(false)}>Cancelar</button>
          </div>
        </form>
      ) : (
        <button type="button" onClick={() => setShowForm(true)}
          style={{ display: "inline-flex", alignItems: "center", gap: 6, marginTop: "var(--sp-2)", fontSize: "var(--font-sm)", color: "var(--blue)", background: "none", border: "none", cursor: "pointer", padding: 0 }}>
          <Plus size={14} /> Nova pendência
        </button>
      )}

      {feedback && <p style={{ fontSize: "var(--font-sm)", color: "var(--green)", marginTop: "var(--sp-2)" }}>{feedback}</p>}
    </fieldset>
  );
}
