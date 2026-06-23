"use client";

import { useEffect, useState } from "react";
import { CheckCircle2, ChevronLeft, ChevronRight, Pencil, Plus, Search, Trash2, XCircle, X, FileText } from "lucide-react";
import {
  createModuleRecordAction,
  updateModuleRecordAction,
  deleteModuleRecordAction,
  fetchLocationsAction,
} from "@/app/actions";

const CHECKLIST_LABELS = [
  "Porta da entrada, nº do quarto e arco da porta estão limpos?",
  "Pavimento aspirado, piso sem manchas, aromatizado e rejunte limpo?",
  "As escadas e garagem estão limpas?",
  "Bom funcionamento das luzes, interruptores e tomadas?",
  "Temperatura amena do quarto? Ligar ar condicionado.",
  "Telefone está com funcionamento pleno e limpo?",
  "Sofás, camas e cabeceiras limpas?",
  "Televisão limpa e funcionando?",
  "Mini bar limpo por fora e por dentro?",
  "Material de informação (cardápios) completos e em bom estado?",
  "Conferiu os itens do frigobar?",
  "Conferiu os itens do minibar?",
  "Conferiu os itens do sex shop?",
  "Conferiu os utensílios que precisam estar na suíte?",
  "Presença de sujeiras, manchas, desbotamentos, buracos, infiltração ou mudança na cor das pinturas?",
  "Conferiu as automatizações (painel de comando)?",
  "Os mobiliários limpos e em bom estado de uso?",
  "O papel de parede está limpo e em bom estado?",
  "Os vidros estão limpos e sem danos?",
  "Os utensílios como bandejas, copos, estão devidamente limpos?",
  "As embalagens dos comestíveis estão limpas, sem poeira?",
  "Lacre de higienização do vaso sanitário conforme treinamento?",
  "Espelho limpo e em bom estado?",
  "Conferir temperatura da água dos chuveiros e hidromassagem?",
  "Conferiu os itens de consumo do banheiro?",
  "Secador funcionando?",
  "As louças e metais do banheiro estão limpos e em bom uso?",
  "Banheiro aromatizado?",
  "Observações gerais do quarto",
  "Observações gerais do banheiro",
];

interface PayloadItem {
  item: string;
  valuation: string;
  register?: string | null;
  occurrence_id?: number | null;
}

interface InspectionPayload {
  date?: string;
  maid?: string;
  obs?: string | null;
  location_id?: number;
  items?: PayloadItem[];
}

export interface InspectionRecord {
  id: number;
  title: string;
  description: string | null;
  category: string | null;
  owner: string;
  status: string;
  payload: InspectionPayload | null;
  updated_at: string;
}

interface LocationOption {
  id: number;
  name: string;
}

interface Props {
  records: InspectionRecord[];
  total: number;
  page: number;
  pageSize: number;
  search: string;
}

export function InspectionViewer({ records, total, page, pageSize, search: initialSearch }: Props) {
  const [locations, setLocations] = useState<LocationOption[]>([]);
  useEffect(() => { fetchLocationsAction().then(setLocations); }, []);
  const [query, setQuery] = useState(initialSearch);
  const [selected, setSelected] = useState<InspectionRecord | null>(null);
  const [editing, setEditing] = useState<InspectionRecord | "new" | null>(null);
  const [toast, setToast] = useState("");
  const pages = Math.max(1, Math.ceil(total / pageSize));

  function showToast(msg: string) { setToast(msg); setTimeout(() => setToast(""), 2600); }

  function navigate(newPage: number) {
    const params = new URLSearchParams();
    params.set("page", String(newPage));
    if (query) params.set("search", query);
    window.location.href = `/inspecoes?${params}`;
  }

  function handleSearch() {
    const params = new URLSearchParams();
    if (query) params.set("search", query);
    window.location.href = `/inspecoes?${params}`;
  }

  async function handleDelete(r: InspectionRecord) {
    if (!confirm(`Excluir "${r.title}"? Esta ação não pode ser desfeita.`)) return;
    const result = await deleteModuleRecordAction("inspecoes", r.id);
    if (result.ok) {
      showToast("Inspeção excluída.");
      setTimeout(() => window.location.reload(), 500);
    } else {
      showToast(result.error ?? "Erro ao excluir.");
    }
  }

  return (
    <>
      <header className="module-heading">
        <div>
          <p className="eyebrow">Inspeções</p>
          <h1>Conferências de Suíte</h1>
          <p>{total} inspeções registradas</p>
        </div>
        <button className="primary-button" onClick={() => setEditing("new")}>
          <Plus size={18} /> Nova conferência
        </button>
      </header>

      <section className="module-panel">
        <div className="module-toolbar">
          <label>
            <Search size={18} />
            <input
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              onKeyDown={(e) => e.key === "Enter" && handleSearch()}
              placeholder="Buscar por título, camareira..."
            />
          </label>
        </div>

        {records.length === 0 ? (
          <div className="module-state">
            <Search size={30} />
            <strong>Nenhum resultado</strong>
            <span>Ajuste os filtros ou crie uma nova conferência.</span>
          </div>
        ) : (
          <div className="module-table-wrap">
            <table>
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Título</th>
                  <th>Camareira</th>
                  <th>Local</th>
                  <th>Data</th>
                  <th>Itens</th>
                  <th>Responsável</th>
                  <th>Status</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                {records.map((r) => {
                  const p = r.payload;
                  const itemCount = p?.items?.length ?? 0;
                  const okCount = p?.items?.filter((i) => i.valuation === "sim").length ?? 0;
                  const locName = p?.location_id ? locations.find((l) => l.id === p.location_id)?.name ?? "—" : "—";
                  return (
                    <tr key={r.id} onClick={() => setSelected(r)} style={{ cursor: "pointer" }}>
                      <td className="protocol">#{r.id}</td>
                      <td><strong>{r.title}</strong></td>
                      <td>{p?.maid ?? "—"}</td>
                      <td>{locName}</td>
                      <td className="muted">
                        {p?.date
                          ? new Intl.DateTimeFormat("pt-BR").format(new Date(p.date + "T12:00:00"))
                          : new Intl.DateTimeFormat("pt-BR").format(new Date(r.updated_at))}
                      </td>
                      <td>
                        <span style={{ display: "inline-flex", alignItems: "center", gap: 4, color: okCount === itemCount ? "var(--green)" : "var(--orange)" }}>
                          <CheckCircle2 size={14} /> {okCount}/{itemCount}
                        </span>
                      </td>
                      <td>{r.owner}</td>
                      <td><span className={r.status === "Concluído" ? "status status-done" : "status status-progress"}>{r.status}</span></td>
                      <td>
                        <div className="row-actions">
                          <button onClick={(e) => { e.stopPropagation(); setEditing(r); }} aria-label="Editar"><Pencil size={16} /></button>
                          <button onClick={(e) => { e.stopPropagation(); handleDelete(r); }} aria-label="Excluir"><Trash2 size={16} /></button>
                        </div>
                      </td>
                    </tr>
                  );
                })}
              </tbody>
            </table>
          </div>
        )}

        <footer className="module-pagination">
          <span>{total} registro(s)</span>
          <div>
            <button disabled={page <= 1} onClick={() => navigate(page - 1)}><ChevronLeft /></button>
            <span>Página {page} de {pages}</span>
            <button disabled={page >= pages} onClick={() => navigate(page + 1)}><ChevronRight /></button>
          </div>
        </footer>
      </section>

      {/* Drawer de detalhes */}
      {selected && !editing && (
        <>
          <button className="panel-backdrop" onClick={() => setSelected(null)} aria-label="Fechar detalhes" />
          <aside className="record-drawer" style={{ maxWidth: 560 }}>
            <header>
              <div>
                <span>#{selected.id} · {selected.category}</span>
                <h2>{selected.title}</h2>
              </div>
              <button className="icon-button" onClick={() => setSelected(null)}><X /></button>
            </header>
            <dl>
              {selected.payload?.maid && <div><dt>Camareira</dt><dd>{selected.payload.maid}</dd></div>}
              {selected.payload?.location_id && <div><dt>Local</dt><dd>{locations.find((l) => l.id === selected.payload!.location_id)?.name ?? `#${selected.payload.location_id}`}</dd></div>}
              {selected.payload?.date && <div><dt>Data</dt><dd>{new Intl.DateTimeFormat("pt-BR").format(new Date(selected.payload.date + "T12:00:00"))}</dd></div>}
              <div><dt>Responsável</dt><dd>{selected.owner}</dd></div>
              <div><dt>Status</dt><dd><span className={selected.status === "Concluído" ? "status status-done" : "status status-progress"}>{selected.status}</span></dd></div>
              {selected.payload?.obs && <div><dt>Observação</dt><dd>{selected.payload.obs}</dd></div>}
            </dl>
            {selected.payload?.items && selected.payload.items.length > 0 && (
              <div style={{ padding: "0 var(--sp-4) var(--sp-4)" }}>
                <h3 style={{ fontSize: "var(--font-base)", fontWeight: 600, marginBottom: "var(--sp-3)", display: "flex", alignItems: "center", gap: 6 }}>
                  <FileText size={15} /> Checklist ({selected.payload.items.filter((i) => i.valuation === "sim").length}/{selected.payload.items.length})
                </h3>
                <div style={{ display: "flex", flexDirection: "column", gap: "var(--sp-1)" }}>
                  {selected.payload.items.map((item, i) => {
                    const ok = item.valuation === "sim";
                    const label = CHECKLIST_LABELS[i] ?? `Item ${Number(item.item) + 1}`;
                    return (
                      <div key={i} style={{
                        display: "flex", alignItems: "flex-start", gap: "var(--sp-2)",
                        padding: "var(--sp-2) var(--sp-3)",
                        background: ok ? "rgba(16,185,129,0.06)" : "rgba(239,68,68,0.06)",
                        borderRadius: "var(--radius-sm)", borderLeft: `3px solid ${ok ? "var(--green)" : "var(--red)"}`,
                      }}>
                        {ok ? <CheckCircle2 size={16} style={{ color: "var(--green)", flexShrink: 0, marginTop: 2 }} /> : <XCircle size={16} style={{ color: "var(--red)", flexShrink: 0, marginTop: 2 }} />}
                        <div style={{ flex: 1 }}>
                          <span style={{ fontSize: "var(--font-sm)", lineHeight: 1.4 }}>{label}</span>
                          {item.register && <p style={{ fontSize: "var(--font-xs)", color: "var(--muted)", margin: "2px 0 0" }}>{item.register}</p>}
                        </div>
                      </div>
                    );
                  })}
                </div>
              </div>
            )}
            <footer>
              <button onClick={() => { setSelected(null); setEditing(selected); }}><Pencil size={16} /> Editar</button>
              <button onClick={() => { setSelected(null); handleDelete(selected); }}><Trash2 size={16} /> Excluir</button>
            </footer>
          </aside>
        </>
      )}

      {/* Modal de criação/edição */}
      {editing && <InspectionFormModal record={editing === "new" ? null : editing} locations={locations} onClose={() => setEditing(null)} onSaved={(msg) => { setEditing(null); showToast(msg); setTimeout(() => window.location.reload(), 500); }} />}

      {toast && <div className="module-toast" role="status">{toast}</div>}
    </>
  );
}

function InspectionFormModal({ record, locations, onClose, onSaved }: {
  record: InspectionRecord | null;
  locations: LocationOption[];
  onClose: () => void;
  onSaved: (msg: string) => void;
}) {
  const isNew = !record;
  const existing = record?.payload;

  const [date, setDate] = useState(existing?.date ?? new Date().toISOString().split("T")[0]);
  const [maid, setMaid] = useState(existing?.maid ?? "");
  const [locationId, setLocationId] = useState(existing?.location_id ? String(existing.location_id) : "");
  const [obs, setObs] = useState(existing?.obs ?? "");
  const [status, setStatus] = useState(record?.status ?? "Em andamento");
  const [items, setItems] = useState<PayloadItem[]>(
    existing?.items ?? CHECKLIST_LABELS.map((_, i) => ({ item: String(i), valuation: "sim", register: null, occurrence_id: null }))
  );
  const [saving, setSaving] = useState(false);
  const [error, setError] = useState<string | null>(null);

  function toggleItem(index: number) {
    setItems((prev) => prev.map((item, i) => i === index ? { ...item, valuation: item.valuation === "sim" ? "nao" : "sim" } : item));
  }

  function setRegister(index: number, value: string) {
    setItems((prev) => prev.map((item, i) => i === index ? { ...item, register: value || null } : item));
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    if (!maid.trim()) return;
    setSaving(true);
    setError(null);

    const title = `Conferência ${date} — ${maid.trim()}`;
    const payload = { date, maid: maid.trim(), obs: obs.trim() || null, location_id: locationId ? Number(locationId) : undefined, items };

    const body = {
      title,
      category: "Conferência de Suíte",
      status,
      payload,
    };

    const result = isNew
      ? await createModuleRecordAction("inspecoes", body)
      : await updateModuleRecordAction("inspecoes", record.id, body);

    setSaving(false);
    if (result.ok) {
      onSaved(isNew ? "Conferência criada." : "Conferência atualizada.");
    } else {
      setError(result.error ?? "Erro ao salvar.");
    }
  }

  const okCount = items.filter((i) => i.valuation === "sim").length;

  return (
    <div className="modal-layer" role="presentation">
      <section className="record-modal" role="dialog" aria-modal="true" style={{ maxWidth: 640, maxHeight: "90vh", overflow: "auto" }}>
        <header>
          <div>
            <span>{isNew ? "Nova conferência" : `#${record.id}`}</span>
            <h2>{isNew ? "Nova conferência de suíte" : "Editar conferência"}</h2>
          </div>
          <button className="icon-button" onClick={onClose}><X /></button>
        </header>

        <form onSubmit={handleSubmit}>
          {error && <div className="kanban-form-error">{error}</div>}

          <div className="form-grid">
            <label>Data *<input type="date" value={date} onChange={(e) => setDate(e.target.value)} required /></label>
            <label>Camareira *<input value={maid} onChange={(e) => setMaid(e.target.value)} required placeholder="Nome da camareira" /></label>
          </div>

          <div className="form-grid">
            <label>Local
              <select value={locationId} onChange={(e) => setLocationId(e.target.value)}>
                <option value="">Sem local</option>
                {locations.map((l) => <option key={l.id} value={l.id}>{l.name}</option>)}
              </select>
            </label>
            <label>Status
              <select value={status} onChange={(e) => setStatus(e.target.value)}>
                <option>Em andamento</option>
                <option>Concluído</option>
              </select>
            </label>
          </div>

          <fieldset className="form-section">
            <legend><FileText size={14} /> Checklist ({okCount}/{items.length})</legend>
            <div style={{ display: "flex", flexDirection: "column", gap: "var(--sp-1)" }}>
              {items.map((item, i) => {
                const ok = item.valuation === "sim";
                const label = CHECKLIST_LABELS[i] ?? `Item ${i + 1}`;
                return (
                  <div key={i} style={{
                    display: "flex", alignItems: "flex-start", gap: "var(--sp-2)",
                    padding: "var(--sp-2) var(--sp-3)",
                    background: ok ? "rgba(16,185,129,0.06)" : "rgba(239,68,68,0.06)",
                    borderRadius: "var(--radius-sm)", borderLeft: `3px solid ${ok ? "var(--green)" : "var(--red)"}`,
                    cursor: "pointer",
                  }}>
                    <button type="button" onClick={() => toggleItem(i)} style={{ background: "none", border: "none", cursor: "pointer", padding: 0, marginTop: 2, flexShrink: 0 }}>
                      {ok ? <CheckCircle2 size={18} style={{ color: "var(--green)" }} /> : <XCircle size={18} style={{ color: "var(--red)" }} />}
                    </button>
                    <div style={{ flex: 1, minWidth: 0 }}>
                      <span style={{ fontSize: "var(--font-sm)", lineHeight: 1.4 }}>{label}</span>
                      {(!ok || item.register) && (
                        <input
                          type="text"
                          value={item.register ?? ""}
                          onChange={(e) => setRegister(i, e.target.value)}
                          placeholder="Observação..."
                          onClick={(e) => e.stopPropagation()}
                          style={{ marginTop: 4, fontSize: "var(--font-xs)", padding: "2px 6px", width: "100%" }}
                        />
                      )}
                    </div>
                  </div>
                );
              })}
            </div>
          </fieldset>

          <label>Observação geral<textarea value={obs} onChange={(e) => setObs(e.target.value)} rows={2} placeholder="Observações adicionais..." /></label>

          <footer>
            <button type="button" onClick={onClose}>Cancelar</button>
            <button type="submit" disabled={saving || !maid.trim()}>
              {saving ? "Salvando..." : isNew ? "Criar conferência" : "Salvar"}
            </button>
          </footer>
        </form>
      </section>
    </div>
  );
}
