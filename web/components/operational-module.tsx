"use client";

import { createFiscalRequestAction, createOccurrenceAction, deleteFiscalRequestAction, deleteOccurrenceAction, logoutAction, updateFiscalRequestAction, updateOccurrenceAction } from "@/app/actions";
import type { TenantUser } from "@/lib/api";
import { moduleDefinitions, navigationModules, type HistoryEntry, type ModuleDefinition, type ModuleRecord } from "@/lib/module-definitions";
import {
  Bell, Building2, ChevronLeft, ChevronRight, ClipboardCheck, Download, FileClock,
  FileText, HardHat, Home, Menu, MessageSquare, Paperclip, Pencil, Plus, Receipt, RefreshCw, Search,
  Send, Settings, ShieldCheck, Trash2, Users, Wrench, X,
} from "lucide-react";
import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { useEffect, useMemo, useRef, useState } from "react";
import { FiscalRequestForm } from "./fiscal-request-form";
import { SlaIndicator } from "./sla-indicator";

const icons: Record<string, typeof FileClock> = { ocorrencias: FileClock, reunioes: Users, "relatorios-turno": FileText, inspecoes: ClipboardCheck, "diarios-obra": HardHat, manutencao: Wrench, "solicitacoes-fiscais": Receipt };
const pageSize = 5;

function statusClass(status: string) {
  if (["Concluído", "Ativo", "Publicado"].includes(status)) return "status status-done";
  if (["Aguardando", "Pendente", "Rascunho"].includes(status)) return "status status-waiting";
  return "status status-progress";
}

export function OperationalModule({ definition, user }: { definition: ModuleDefinition; user: TenantUser }) {
  const storageKey = `registro:${user.company_id}:${definition.slug}`;
  const isFiscal = definition.slug === "solicitacoes-fiscais";
  const isOcorrencias = definition.slug === "ocorrencias";
  const isApiBacked = definition.source === "api";
  const canMutate = !isApiBacked || isFiscal || isOcorrencias;
  const searchParams = useSearchParams();
  const router = useRouter();
  const sp = definition.serverPagination;
  const [records, setRecords] = useState<ModuleRecord[]>(definition.records);
  const [ready, setReady] = useState(false);
  const [query, setQuery] = useState(sp?.search ?? "");
  const [status, setStatus] = useState("Todos");
  const [page, setPage] = useState(sp?.page ?? 1);
  const [mobileMenu, setMobileMenu] = useState(false);
  const [editing, setEditing] = useState<ModuleRecord | "new" | null>(null);
  const [selected, setSelected] = useState<ModuleRecord | null>(null);
  const [toast, setToast] = useState("");

  useEffect(() => {
    if (isApiBacked) {
      setRecords(definition.records);
    } else {
      const saved = window.localStorage.getItem(storageKey);
      if (saved) setRecords(JSON.parse(saved) as ModuleRecord[]);
    }
    setReady(true);
  }, [definition.records, isApiBacked, storageKey]);

  useEffect(() => {
    if (canMutate && searchParams.get("new") === "1") setEditing("new");
  }, [canMutate, searchParams]);

  useEffect(() => {
    const protocol = searchParams.get("protocol");
    if (!protocol || !isFiscal) return;
    const record = records.find((item) => `REG-${String(item.id).padStart(6, "0")}` === protocol);
    if (record) setSelected(record);
  }, [isFiscal, records, searchParams]);

  useEffect(() => {
    if (!isApiBacked) return;

    const refresh = () => router.refresh();
    const interval = window.setInterval(refresh, 15_000);
    window.addEventListener("focus", refresh);
    document.addEventListener("visibilitychange", refresh);

    return () => {
      window.clearInterval(interval);
      window.removeEventListener("focus", refresh);
      document.removeEventListener("visibilitychange", refresh);
    };
  }, [isApiBacked, router]);

  function persist(next: ModuleRecord[], message: string) {
    setRecords(next);
    window.localStorage.setItem(storageKey, JSON.stringify(next));
    setToast(message);
    window.setTimeout(() => setToast(""), 2600);
  }

  const statuses = useMemo(() => ["Todos", ...new Set(records.map((record) => record.status))], [records]);
  const filtered = useMemo(() => {
    if (sp) return records;
    return records.filter((record) => {
      const text = `${record.id} ${record.title} ${record.category} ${record.owner} ${record.status}`.toLocaleLowerCase("pt-BR");
      return (!query || text.includes(query.toLocaleLowerCase("pt-BR"))) && (status === "Todos" || record.status === status);
    });
  }, [query, records, status, sp]);
  const totalItems = sp ? sp.total : filtered.length;
  const effectivePageSize = sp ? sp.pageSize : pageSize;
  const pages = Math.max(1, Math.ceil(totalItems / effectivePageSize));
  const visible = sp ? filtered : filtered.slice((Math.min(page, pages) - 1) * pageSize, Math.min(page, pages) * pageSize);

  const searchTimeoutRef = useRef<ReturnType<typeof setTimeout> | null>(null);
  function handleServerSearch(value: string) {
    setQuery(value);
    if (!sp) { setPage(1); return; }
    if (searchTimeoutRef.current) clearTimeout(searchTimeoutRef.current);
    searchTimeoutRef.current = setTimeout(() => {
      const params = new URLSearchParams();
      if (value) params.set("search", value);
      router.push(`/${definition.slug}?${params.toString()}`);
    }, 400);
  }
  function handleServerPage(newPage: number) {
    setPage(newPage);
    if (!sp) return;
    const params = new URLSearchParams();
    params.set("page", String(newPage));
    if (query) params.set("search", query);
    router.push(`/${definition.slug}?${params.toString()}`);
  }

  function formatNow() {
    return new Date().toLocaleString("pt-BR", { day: "2-digit", month: "2-digit", year: "numeric", hour: "2-digit", minute: "2-digit" });
  }

  function diffChanges(before: ModuleRecord, after: { title: string; category: string; owner: string; status: string; description: string }): string {
    const labels: Record<string, string> = { title: "Título", category: "Categoria", owner: "Responsável", status: "Status", description: "Descrição" };
    const parts: string[] = [];
    for (const key of Object.keys(labels) as (keyof typeof labels)[]) {
      const old = (before as Record<string, unknown>)[key] ?? "";
      const cur = (after as Record<string, unknown>)[key] ?? "";
      if (String(old) !== String(cur)) parts.push(`${labels[key]}: "${old}" → "${cur}"`);
    }
    return parts.join("; ");
  }

  function parseNotifyUsers(raw: string): string[] {
    return raw.split(",").map((s) => s.trim()).filter(Boolean);
  }

  async function saveRecord(formData: FormData) {
    const current = editing === "new" ? null : editing;
    const now = formatNow();
    const fields = {
      title: String(formData.get("title")), category: String(formData.get("category")),
      owner: String(formData.get("owner")), status: String(formData.get("status")),
      description: String(formData.get("description") ?? ""),
    };

    if (isOcorrencias && isApiBacked) {
      const statusMap: Record<string, number> = { "Em andamento": 1, "Concluido": 2, "Aguardando": 3 };
      const apiBody = {
        title: fields.title,
        description: fields.description || undefined,
        status: statusMap[fields.status] ?? 1,
      };
      if (current) {
        const result = await updateOccurrenceAction(current.id, apiBody);
        if (!result.ok) { setToast(result.error ?? "Erro ao atualizar."); return; }
      } else {
        const result = await createOccurrenceAction(apiBody);
        if (!result.ok) { setToast(result.error ?? "Erro ao criar."); return; }
      }
      setEditing(null);
      setToast(`${definition.singular} ${current ? "atualizada" : "criada"} com sucesso.`);
      window.setTimeout(() => setToast(""), 2600);
      router.refresh();
      return;
    }

    const entry: HistoryEntry = current
      ? { type: "change", user: user.name, date: now, changes: diffChanges(current, fields) }
      : { type: "create", user: user.name, date: now, message: `Criou ${definition.singular}` };

    const notifyUsers = parseNotifyUsers(String(formData.get("notifyUsers") ?? ""));
    const prevHistory = current?.history ?? [];
    const record: ModuleRecord = {
      id: current?.id ?? Math.max(0, ...records.map((item) => item.id)) + 1,
      ...fields, notifyUsers, updatedAt: now, history: [...prevHistory, entry],
    };
    const next = current ? records.map((item) => item.id === current.id ? record : item) : [record, ...records];
    persist(next, `${definition.singular} ${current ? "atualizado" : "criado"} com sucesso.`);
    setEditing(null);
  }

  async function saveFiscalRecord(data: Partial<ModuleRecord>) {
    const current = editing === "new" ? null : editing;

    if (isApiBacked) {
      const payload: Record<string, unknown> = {};
      for (const key of ["requestType", "reservationNumber", "invoiceNumber", "checkoutDate", "taxpayerDoc", "taxpayerName", "taxpayerAddress", "taxpayerEmail", "cancellationReason", "correction", "slaDeadline"] as const) {
        if (data[key]) payload[key] = data[key];
      }
      if (current) {
        const result = await updateFiscalRequestAction(current.id, {
          request_type: data.requestType ?? data.category,
          title: data.title,
          apartment: data.apartment,
          requester: data.owner,
          description: data.description,
          status: data.status,
          payload,
        });
        if (!result.ok) { setToast(result.error ?? "Erro ao atualizar."); return; }
      } else {
        const result = await createFiscalRequestAction({
          request_type: data.requestType ?? data.category ?? "",
          title: data.title ?? "",
          apartment: data.apartment,
          requester: data.owner ?? user.name,
          description: data.description,
          status: data.status ?? "Em andamento",
          payload,
        });
        if (!result.ok) { setToast(result.error ?? "Erro ao criar."); return; }
      }
      setEditing(null);
      setToast(`${definition.singular} ${current ? "atualizada" : "criada"} com sucesso.`);
      window.setTimeout(() => setToast(""), 2600);
      router.refresh();
      return;
    }

    const now = formatNow();
    const entry: HistoryEntry = current
      ? { type: "change", user: user.name, date: now, changes: diffChanges(current, { title: data.title ?? "", category: data.category ?? "", owner: data.owner ?? "", status: data.status ?? "", description: data.description ?? "" }) }
      : { type: "create", user: user.name, date: now, message: `Criou ${definition.singular}` };
    const prevHistory = current?.history ?? [];
    const record: ModuleRecord = {
      id: current?.id ?? Math.max(0, ...records.map((item) => item.id)) + 1,
      ...data, updatedAt: now, history: [...prevHistory, entry],
    } as ModuleRecord;
    const next = current ? records.map((item) => item.id === current.id ? record : item) : [record, ...records];
    persist(next, `${definition.singular} ${current ? "atualizada" : "criada"} com sucesso.`);
    setEditing(null);
  }

  function addComment(record: ModuleRecord, message: string) {
    if (!message.trim()) return;
    const now = formatNow();
    const entry: HistoryEntry = { type: "comment", user: user.name, date: now, message: message.trim() };
    const updated = { ...record, updatedAt: now, history: [...(record.history ?? []), entry] };
    const next = records.map((item) => item.id === record.id ? updated : item);
    persist(next, "Comentário adicionado.");
    setSelected(updated);
  }

  async function remove(record: ModuleRecord) {
    if (!window.confirm(`Excluir "${record.title}"? Esta ação não pode ser desfeita.`)) return;
    if (isApiBacked && (isFiscal || isOcorrencias)) {
      const result = isFiscal
        ? await deleteFiscalRequestAction(record.id)
        : await deleteOccurrenceAction(record.id);
      if (!result.ok) { setToast(result.error ?? "Erro ao excluir."); return; }
      setSelected(null);
      setToast(`${definition.singular} excluida.`);
      window.setTimeout(() => setToast(""), 2600);
      router.refresh();
      return;
    }
    persist(records.filter((item) => item.id !== record.id), `${definition.singular} excluído.`);
    setSelected(null);
  }

  function exportCsv() {
    const rows = [["ID", "Título", "Categoria", "Responsável", "Status", "Atualização"], ...filtered.map((item) => [item.id, item.title, item.category, item.owner, item.status, item.updatedAt])];
    const csv = rows.map((row) => row.map((value) => `"${String(value).replaceAll('"', '""')}"`).join(",")).join("\n");
    const url = URL.createObjectURL(new Blob([csv], { type: "text/csv;charset=utf-8" }));
    const anchor = document.createElement("a"); anchor.href = url; anchor.download = `${definition.slug}.csv`; anchor.click(); URL.revokeObjectURL(url);
    setToast("Arquivo CSV gerado.");
  }

  const initials = user.name.split(" ").slice(0, 2).map((part) => part[0]).join("").toUpperCase();

  return <div className="module-shell">
    <aside className={`module-sidebar ${mobileMenu ? "is-open" : ""}`}>
      <Link href="/dashboard" className="module-brand"><span>R</span><div><strong>Registro</strong><small>Gestão operacional</small></div></Link>
      <nav>
        <Link href="/dashboard" className="module-nav-item"><Home size={18}/>Visão geral</Link>
        {navigationModules.map((slug) => { const item = moduleDefinitions[slug]; const Icon = icons[slug as keyof typeof icons]; return <Link key={slug} href={`/${slug}`} className={`module-nav-item ${definition.slug === slug ? "active" : ""}`}><Icon size={18}/>{item.title}</Link>; })}
        <span className="module-nav-label">Administração</span>
        <Link href="/cadastros" className={`module-nav-item ${definition.slug === "cadastros" ? "active" : ""}`}><Building2 size={18}/>Cadastros</Link>
        <Link href="/usuarios" className={`module-nav-item ${definition.slug === "usuarios" ? "active" : ""}`}><ShieldCheck size={18}/>Usuários e acesso</Link>
        <Link href="/mural" className={`module-nav-item ${definition.slug === "mural" ? "active" : ""}`}><Bell size={18}/>Mural de avisos</Link>
      </nav>
      <Link href="/configuracoes" className={`module-nav-item module-settings ${definition.slug === "configuracoes" ? "active" : ""}`}><Settings size={18}/>Configurações</Link>
    </aside>
    {mobileMenu ? <button className="backdrop" aria-label="Fechar menu" onClick={() => setMobileMenu(false)}/> : null}
    <header className="module-topbar"><button className="icon-button mobile-menu" onClick={() => setMobileMenu(true)}><Menu/></button><div><span>Empresa demonstração</span><strong>{definition.title}</strong></div><Link href="/minha-conta" className="module-user"><span>{initials}</span><div><strong>{user.name}</strong><small>{user.role_name}</small></div></Link></header>
    <main className="module-main">
      <header className="module-heading"><div><p className="eyebrow">Operação</p><h1>{definition.title}</h1><p>{definition.description}</p>{isApiBacked && !isFiscal && !isOcorrencias ? <small className="api-readonly-notice">Dados da API em modo de leitura até a liberação dos endpoints de mutação.</small> : null}</div>{canMutate && definition.layout !== "settings" && definition.layout !== "profile" ? <button className="primary-button" onClick={() => setEditing("new")}><Plus size={18}/>{definition.action}</button> : null}</header>

      {definition.layout === "settings" ? <SettingsForm storageKey={storageKey} onSaved={() => setToast("Configurações salvas com sucesso.")}/> : definition.layout === "profile" ? <ProfileForm user={user} onSaved={() => setToast("Perfil atualizado localmente.")}/> : <section className="module-panel">
        <div className="module-toolbar"><label><Search size={18}/><input value={query} onChange={(event) => handleServerSearch(event.target.value)} placeholder={`Buscar em ${definition.title.toLocaleLowerCase("pt-BR")}`}/></label>{!sp ? <select value={status} onChange={(event) => { setStatus(event.target.value); setPage(1); }}>{statuses.map((item) => <option key={item}>{item}</option>)}</select> : null}{canMutate && !sp ? <button onClick={() => { setRecords(definition.records); localStorage.removeItem(storageKey); setToast("Dados fictícios restaurados."); }} title="Restaurar dados"><RefreshCw size={17}/></button> : null}<button onClick={exportCsv}><Download size={17}/> Exportar</button></div>
        {!ready ? <div className="module-state">Carregando registros…</div> : !visible.length ? <div className="module-state"><Search size={30}/><strong>Nenhum resultado</strong><span>Ajuste os filtros ou crie um novo registro.</span></div> : definition.layout === "cards" ? <div className="notice-grid">{visible.map((record) => <article key={record.id} onClick={() => setSelected(record)}><span>{record.category}</span><h2>{record.title}</h2><p>{record.description}</p><footer><small>{record.owner} · {record.updatedAt}</small><i className={statusClass(record.status)}>{record.status}</i></footer></article>)}</div> : <div className="module-table-wrap"><table><thead><tr><th>ID</th><th>{definition.singular}</th>{isFiscal && <th>UH</th>}<th>Categoria</th><th>Responsável</th><th>Status</th>{isFiscal && <th>SLA</th>}<th>Atualização</th>{canMutate ? <th>Ações</th> : null}</tr></thead><tbody>{visible.map((record) => <tr key={record.id} onClick={() => setSelected(record)}><td className="protocol">#{record.id}</td><td><strong>{record.title}</strong></td>{isFiscal && <td>{record.apartment ?? "—"}</td>}<td>{record.category}</td><td>{record.owner}</td><td><span className={statusClass(record.status)}>{record.status}</span></td>{isFiscal && <td>{record.slaDeadline ? <SlaIndicator deadline={record.slaDeadline}/> : "—"}</td>}<td className="muted">{record.updatedAt}</td>{canMutate ? <td><div className="row-actions"><button onClick={(event) => { event.stopPropagation(); setEditing(record); }} aria-label="Editar"><Pencil size={16}/></button><button onClick={(event) => { event.stopPropagation(); remove(record); }} aria-label="Excluir"><Trash2 size={16}/></button></div></td> : null}</tr>)}</tbody></table></div>}
        <footer className="module-pagination"><span>{totalItems} registro(s)</span><div><button disabled={page <= 1} onClick={() => handleServerPage(page - 1)}><ChevronLeft/></button><span>Pagina {Math.min(page, pages)} de {pages}</span><button disabled={page >= pages} onClick={() => handleServerPage(page + 1)}><ChevronRight/></button></div></footer>
      </section>}
    </main>

    {editing ? <div className="modal-layer" role="presentation"><section className={`record-modal${editing !== "new" && editing.history?.length ? " has-timeline" : ""}${isFiscal ? " fiscal-modal" : ""}`} role="dialog" aria-modal="true"><header><div><span>{editing === "new" ? "Novo registro" : `#${editing.id}`}</span><h2>{editing === "new" ? definition.action : `Editar ${definition.singular}`}</h2></div><button className="icon-button" onClick={() => setEditing(null)}><X/></button></header>
      {isFiscal ? <FiscalRequestForm record={editing} userName={user.name} onSave={saveFiscalRecord} onCancel={() => setEditing(null)}/> : <form action={saveRecord}><label>Título<input name="title" required defaultValue={editing === "new" ? "" : editing.title}/></label><div className="form-grid"><label>Categoria<input name="category" required defaultValue={editing === "new" ? "Geral" : editing.category}/></label><label>Status<select name="status" defaultValue={editing === "new" ? "Em andamento" : editing.status}><option>Em andamento</option><option>Aguardando</option><option>Agendada</option><option>Ativo</option><option>Publicado</option><option>Rascunho</option><option>Concluído</option></select></label></div><label>Responsável<input name="owner" required defaultValue={editing === "new" ? user.name : editing.owner}/></label><label>Notificar<input name="notifyUsers" placeholder="Nomes separados por vírgula" defaultValue={editing === "new" ? "" : (editing.notifyUsers ?? []).join(", ")}/><small className="field-hint">Pessoas ou grupos que serão notificados sobre atualizações.</small></label><label>Descrição<textarea name="description" rows={4} defaultValue={editing === "new" ? "" : editing.description}/></label><footer><button type="button" onClick={() => setEditing(null)}>Cancelar</button><button type="submit">Salvar</button></footer></form>}
      {editing !== "new" && editing.history?.length ? <div className="modal-timeline"><h3><MessageSquare size={15}/>Tratativa</h3><div className="timeline-thread">{editing.history.map((entry, i) => {
        const entryInitials = entry.user.split(" ").slice(0, 2).map((p) => p[0]).join("").toUpperCase();
        return <article key={i} className={`thread-entry thread-${entry.type}`}>
          <div className="thread-avatar">{entryInitials}</div>
          <div className="thread-body">
            <div className="thread-header"><strong>{entry.user}</strong><time>{entry.date}</time></div>
            {entry.type === "comment" && entry.message ? <p className="thread-message">{entry.message}</p> : null}
            {entry.type === "create" ? <p className="thread-system">{entry.message}</p> : null}
            {entry.type === "change" && entry.changes ? <div className="thread-changes">{entry.changes.split("; ").map((c, j) => <span key={j}>{c}</span>)}</div> : null}
          </div>
        </article>;
      })}</div></div> : null}
    </section></div> : null}
    {selected && !editing ? <><button className="panel-backdrop" onClick={() => setSelected(null)} aria-label="Fechar detalhes"/><aside className="record-drawer">
      <header><div><span>#{selected.id} · {selected.category}</span><h2>{selected.title}</h2></div><button className="icon-button" onClick={() => setSelected(null)}><X/></button></header>
      <dl>
        <div><dt>Status</dt><dd><span className={statusClass(selected.status)}>{selected.status}</span></dd></div>
        <div><dt>Responsável</dt><dd>{selected.owner}</dd></div>
        {selected.notifyUsers && selected.notifyUsers.length > 0 && <div><dt>Notificar</dt><dd><div className="notify-chips">{selected.notifyUsers.map((name, i) => <span key={i} className="notify-chip">{name}</span>)}</div></dd></div>}
        {isFiscal && selected.slaDeadline && <div><dt>SLA</dt><dd><SlaIndicator deadline={selected.slaDeadline}/></dd></div>}
        {isFiscal && selected.apartment && <div><dt>UH (Apartamento)</dt><dd>{selected.apartment}</dd></div>}
        {isFiscal && selected.requestType && <div><dt>Tipo da solicitação</dt><dd>{selected.requestType}</dd></div>}
        {isFiscal && selected.reservationNumber && <div><dt>Número da reserva</dt><dd>{selected.reservationNumber}</dd></div>}
        {isFiscal && selected.invoiceNumber && <div><dt>Número da nota</dt><dd>{selected.invoiceNumber}</dd></div>}
        {isFiscal && selected.checkoutDate && <div><dt>Data do check-out</dt><dd>{selected.checkoutDate}</dd></div>}
        {isFiscal && selected.taxpayerDoc && <div><dt>CPF / CNPJ</dt><dd>{selected.taxpayerDoc}</dd></div>}
        {isFiscal && selected.taxpayerName && <div><dt>Nome do tomador</dt><dd>{selected.taxpayerName}</dd></div>}
        {isFiscal && selected.taxpayerAddress && <div><dt>Endereço do tomador</dt><dd>{selected.taxpayerAddress}</dd></div>}
        {isFiscal && selected.taxpayerEmail && <div><dt>E-mail do tomador</dt><dd>{selected.taxpayerEmail}</dd></div>}
        {isFiscal && selected.cancellationReason && <div><dt>Motivo do cancelamento</dt><dd>{selected.cancellationReason}</dd></div>}
        {isFiscal && selected.correction && <div><dt>Correção necessária</dt><dd>{selected.correction}</dd></div>}
        <div><dt>Atualização</dt><dd>{selected.updatedAt}</dd></div>
        <div><dt>Descrição</dt><dd>{selected.description || "Nenhuma descrição informada."}</dd></div>
        {isFiscal && selected.attachments && selected.attachments.length > 0 && <div><dt>Anexos</dt><dd><div className="attachment-grid drawer-attachments">{selected.attachments.map((att, i) => <a key={i} href={att.url} target="_blank" rel="noopener noreferrer" className="attachment-preview">{att.type.startsWith("image/") ? <img src={att.url} alt={att.name}/> : <div className="attachment-file-icon"><Paperclip size={20}/></div>}<span className="attachment-name">{att.name}</span></a>)}</div></dd></div>}
      </dl>
      <div className="record-timeline">
        <h3><MessageSquare size={15}/>Tratativa</h3>
        {selected.history && selected.history.length > 0 ? <div className="timeline-thread">
          {selected.history.map((entry, i) => {
            const entryInitials = entry.user.split(" ").slice(0, 2).map((p) => p[0]).join("").toUpperCase();
            return <article key={i} className={`thread-entry thread-${entry.type}`}>
              <div className="thread-avatar">{entryInitials}</div>
              <div className="thread-body">
                <div className="thread-header"><strong>{entry.user}</strong><time>{entry.date}</time></div>
                {entry.type === "comment" && entry.message ? <p className="thread-message">{entry.message}</p> : null}
                {entry.type === "create" ? <p className="thread-system">{entry.message}</p> : null}
                {entry.type === "change" && entry.changes ? <div className="thread-changes">{entry.changes.split("; ").map((c, j) => <span key={j}>{c}</span>)}</div> : null}
              </div>
            </article>;
          })}
        </div> : <p className="thread-empty">Nenhuma interação registrada.</p>}
        {canMutate ? <CommentInput onSend={(msg) => addComment(selected, msg)}/> : <p className="thread-empty">Comentários serão liberados com a API de mutações.</p>}
      </div>
      {canMutate ? <footer><button onClick={() => setEditing(selected)}><Pencil size={16}/>Editar</button><button onClick={() => remove(selected)}><Trash2 size={16}/>Excluir</button></footer> : null}
    </aside></> : null}
    {toast ? <div className="module-toast" role="status">{toast}</div> : null}
  </div>;
}

function CommentInput({ onSend }: { onSend: (message: string) => void }) {
  const [value, setValue] = useState("");
  return <form className="comment-form" onSubmit={(e) => { e.preventDefault(); onSend(value); setValue(""); }}>
    <input value={value} onChange={(e) => setValue(e.target.value)} placeholder="Escreva um comentário..." />
    <button type="submit" disabled={!value.trim()} aria-label="Enviar"><Send size={16}/></button>
  </form>;
}

function SettingsForm({ storageKey, onSaved }: { storageKey: string; onSaved: () => void }) {
  return <form className="settings-form" action={(data) => { localStorage.setItem(`${storageKey}:preferences`, JSON.stringify(Object.fromEntries(data))); onSaved(); }}><section><h2>Notificações</h2><p>Escolha como deseja acompanhar as atualizações.</p><label className="switch-row"><span><strong>Notificações no sistema</strong><small>Alertas de atividades e menções.</small></span><input name="in_app" type="checkbox" defaultChecked/></label><label className="switch-row"><span><strong>Resumo por e-mail</strong><small>Resumo diário das pendências.</small></span><input name="email_digest" type="checkbox" defaultChecked/></label></section><section><h2>Experiência</h2><p>Preferências aplicadas a este navegador.</p><label>Idioma<select name="language" defaultValue="pt-BR"><option value="pt-BR">Português (Brasil)</option><option value="en">English</option></select></label><label>Página inicial<select name="home" defaultValue="dashboard"><option value="dashboard">Visão geral</option><option value="ocorrencias">Ocorrências</option></select></label></section><button className="primary-button" type="submit">Salvar alterações</button></form>;
}

function ProfileForm({ user, onSaved }: { user: TenantUser; onSaved: () => void }) {
  return <form className="settings-form profile-form" action={onSaved}><section><h2>Dados pessoais</h2><p>Os dados são demonstrativos até existir o endpoint de atualização.</p><label>Nome completo<input name="name" defaultValue={user.name}/></label><label>E-mail<input name="email" type="email" defaultValue={user.email}/></label><label>Cargo<input name="role" value={user.role_name ?? ""} readOnly/></label></section><button className="primary-button" type="submit">Salvar perfil</button><button className="secondary-button" formAction={logoutAction}>Sair da conta</button></form>;
}
