"use client";

import { logoutAction } from "@/app/actions";
import type { TenantUser } from "@/lib/api";
import { moduleDefinitions, navigationModules, type HistoryEntry, type ModuleDefinition, type ModuleRecord } from "@/lib/module-definitions";
import {
  Bell, Building2, ChevronLeft, ChevronRight, ClipboardCheck, Clock, Download, FileClock,
  FileText, HardHat, Home, Menu, Pencil, Plus, RefreshCw, Search, Settings,
  ShieldCheck, Trash2, Users, Wrench, X,
} from "lucide-react";
import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { useEffect, useMemo, useState } from "react";

const icons = { ocorrencias: FileClock, reunioes: Users, "relatorios-turno": FileText, inspecoes: ClipboardCheck, "diarios-obra": HardHat, manutencao: Wrench };
const pageSize = 5;

function statusClass(status: string) {
  if (["Concluído", "Ativo", "Publicado"].includes(status)) return "status status-done";
  if (["Aguardando", "Pendente", "Rascunho"].includes(status)) return "status status-waiting";
  return "status status-progress";
}

export function OperationalModule({ definition, user }: { definition: ModuleDefinition; user: TenantUser }) {
  const storageKey = `registro:${user.company_id}:${definition.slug}`;
  const searchParams = useSearchParams();
  const [records, setRecords] = useState<ModuleRecord[]>(definition.records);
  const [ready, setReady] = useState(false);
  const [query, setQuery] = useState("");
  const [status, setStatus] = useState("Todos");
  const [page, setPage] = useState(1);
  const [mobileMenu, setMobileMenu] = useState(false);
  const [editing, setEditing] = useState<ModuleRecord | "new" | null>(null);
  const [selected, setSelected] = useState<ModuleRecord | null>(null);
  const [toast, setToast] = useState("");

  useEffect(() => {
    const saved = window.localStorage.getItem(storageKey);
    if (saved) setRecords(JSON.parse(saved) as ModuleRecord[]);
    setReady(true);
  }, [storageKey]);

  useEffect(() => {
    if (searchParams.get("new") === "1") setEditing("new");
  }, [searchParams]);

  function persist(next: ModuleRecord[], message: string) {
    setRecords(next);
    window.localStorage.setItem(storageKey, JSON.stringify(next));
    setToast(message);
    window.setTimeout(() => setToast(""), 2600);
  }

  const statuses = useMemo(() => ["Todos", ...new Set(records.map((record) => record.status))], [records]);
  const filtered = useMemo(() => records.filter((record) => {
    const text = `${record.id} ${record.title} ${record.category} ${record.owner} ${record.status}`.toLocaleLowerCase("pt-BR");
    return (!query || text.includes(query.toLocaleLowerCase("pt-BR"))) && (status === "Todos" || record.status === status);
  }), [query, records, status]);
  const pages = Math.max(1, Math.ceil(filtered.length / pageSize));
  const visible = filtered.slice((Math.min(page, pages) - 1) * pageSize, Math.min(page, pages) * pageSize);

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

  function saveRecord(formData: FormData) {
    const current = editing === "new" ? null : editing;
    const now = formatNow();
    const fields = {
      title: String(formData.get("title")), category: String(formData.get("category")),
      owner: String(formData.get("owner")), status: String(formData.get("status")),
      description: String(formData.get("description") ?? ""),
    };

    const entry: HistoryEntry = {
      action: current ? "Editou" : "Criou",
      user: user.name,
      date: now,
      changes: current ? diffChanges(current, fields) : undefined,
    };

    const prevHistory = current?.history ?? [];
    const record: ModuleRecord = {
      id: current?.id ?? Math.max(0, ...records.map((item) => item.id)) + 1,
      ...fields, updatedAt: now, history: [entry, ...prevHistory],
    };
    const next = current ? records.map((item) => item.id === current.id ? record : item) : [record, ...records];
    persist(next, `${definition.singular} ${current ? "atualizado" : "criado"} com sucesso.`);
    setEditing(null);
  }

  function remove(record: ModuleRecord) {
    if (!window.confirm(`Excluir “${record.title}”? Esta ação não pode ser desfeita.`)) return;
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
      <header className="module-heading"><div><p className="eyebrow">Operação</p><h1>{definition.title}</h1><p>{definition.description}</p></div>{definition.layout !== "settings" && definition.layout !== "profile" ? <button className="primary-button" onClick={() => setEditing("new")}><Plus size={18}/>{definition.action}</button> : null}</header>

      {definition.layout === "settings" ? <SettingsForm storageKey={storageKey} onSaved={() => setToast("Configurações salvas com sucesso.")}/> : definition.layout === "profile" ? <ProfileForm user={user} onSaved={() => setToast("Perfil atualizado localmente.")}/> : <section className="module-panel">
        <div className="module-toolbar"><label><Search size={18}/><input value={query} onChange={(event) => { setQuery(event.target.value); setPage(1); }} placeholder={`Buscar em ${definition.title.toLocaleLowerCase("pt-BR")}`}/></label><select value={status} onChange={(event) => { setStatus(event.target.value); setPage(1); }}>{statuses.map((item) => <option key={item}>{item}</option>)}</select><button onClick={() => { setRecords(definition.records); localStorage.removeItem(storageKey); setToast("Dados fictícios restaurados."); }} title="Restaurar dados"><RefreshCw size={17}/></button><button onClick={exportCsv}><Download size={17}/> Exportar</button></div>
        {!ready ? <div className="module-state">Carregando registros…</div> : !visible.length ? <div className="module-state"><Search size={30}/><strong>Nenhum resultado</strong><span>Ajuste os filtros ou crie um novo registro.</span></div> : definition.layout === "cards" ? <div className="notice-grid">{visible.map((record) => <article key={record.id} onClick={() => setSelected(record)}><span>{record.category}</span><h2>{record.title}</h2><p>{record.description}</p><footer><small>{record.owner} · {record.updatedAt}</small><i className={statusClass(record.status)}>{record.status}</i></footer></article>)}</div> : <div className="module-table-wrap"><table><thead><tr><th>ID</th><th>{definition.singular}</th><th>Categoria</th><th>Responsável</th><th>Status</th><th>Atualização</th><th>Ações</th></tr></thead><tbody>{visible.map((record) => <tr key={record.id} onClick={() => setSelected(record)}><td className="protocol">#{record.id}</td><td><strong>{record.title}</strong></td><td>{record.category}</td><td>{record.owner}</td><td><span className={statusClass(record.status)}>{record.status}</span></td><td className="muted">{record.updatedAt}</td><td><div className="row-actions"><button onClick={(event) => { event.stopPropagation(); setEditing(record); }} aria-label="Editar"><Pencil size={16}/></button><button onClick={(event) => { event.stopPropagation(); remove(record); }} aria-label="Excluir"><Trash2 size={16}/></button></div></td></tr>)}</tbody></table></div>}
        <footer className="module-pagination"><span>{filtered.length} registro(s)</span><div><button disabled={page <= 1} onClick={() => setPage((value) => value - 1)}><ChevronLeft/></button><span>Página {Math.min(page, pages)} de {pages}</span><button disabled={page >= pages} onClick={() => setPage((value) => value + 1)}><ChevronRight/></button></div></footer>
      </section>}
    </main>

    {editing ? <div className="modal-layer" role="presentation"><section className="record-modal" role="dialog" aria-modal="true"><header><div><span>{editing === "new" ? "Novo registro" : `#${editing.id}`}</span><h2>{editing === "new" ? definition.action : `Editar ${definition.singular}`}</h2></div><button className="icon-button" onClick={() => setEditing(null)}><X/></button></header><form action={saveRecord}><label>Título<input name="title" required defaultValue={editing === "new" ? "" : editing.title}/></label><div className="form-grid"><label>Categoria<input name="category" required defaultValue={editing === "new" ? "Geral" : editing.category}/></label><label>Status<select name="status" defaultValue={editing === "new" ? "Em andamento" : editing.status}><option>Em andamento</option><option>Aguardando</option><option>Agendada</option><option>Ativo</option><option>Publicado</option><option>Rascunho</option><option>Concluído</option></select></label></div><label>Responsável<input name="owner" required defaultValue={editing === "new" ? user.name : editing.owner}/></label><label>Descrição<textarea name="description" rows={4} defaultValue={editing === "new" ? "" : editing.description}/></label><footer><button type="button" onClick={() => setEditing(null)}>Cancelar</button><button type="submit">Salvar</button></footer></form></section></div> : null}
    {selected && !editing ? <><button className="panel-backdrop" onClick={() => setSelected(null)} aria-label="Fechar detalhes"/><aside className="record-drawer"><header><div><span>#{selected.id} · {selected.category}</span><h2>{selected.title}</h2></div><button className="icon-button" onClick={() => setSelected(null)}><X/></button></header><dl><div><dt>Status</dt><dd><span className={statusClass(selected.status)}>{selected.status}</span></dd></div><div><dt>Responsável</dt><dd>{selected.owner}</dd></div><div><dt>Atualização</dt><dd>{selected.updatedAt}</dd></div><div><dt>Descrição</dt><dd>{selected.description || "Nenhuma descrição informada."}</dd></div></dl>{selected.history && selected.history.length > 0 ? <div className="record-timeline"><h3><Clock size={15}/>Histórico de alterações</h3><ol>{selected.history.map((entry, i) => <li key={i}><div className="timeline-dot"/><div className="timeline-content"><strong>{entry.user}</strong> <span className="timeline-action">{entry.action.toLowerCase()}</span><time>{entry.date}</time>{entry.changes ? <p className="timeline-changes">{entry.changes}</p> : null}</div></li>)}</ol></div> : null}<footer><button onClick={() => setEditing(selected)}><Pencil size={16}/>Editar</button><button onClick={() => remove(selected)}><Trash2 size={16}/>Excluir</button></footer></aside></> : null}
    {toast ? <div className="module-toast" role="status">{toast}</div> : null}
  </div>;
}

function SettingsForm({ storageKey, onSaved }: { storageKey: string; onSaved: () => void }) {
  return <form className="settings-form" action={(data) => { localStorage.setItem(`${storageKey}:preferences`, JSON.stringify(Object.fromEntries(data))); onSaved(); }}><section><h2>Notificações</h2><p>Escolha como deseja acompanhar as atualizações.</p><label className="switch-row"><span><strong>Notificações no sistema</strong><small>Alertas de atividades e menções.</small></span><input name="in_app" type="checkbox" defaultChecked/></label><label className="switch-row"><span><strong>Resumo por e-mail</strong><small>Resumo diário das pendências.</small></span><input name="email_digest" type="checkbox" defaultChecked/></label></section><section><h2>Experiência</h2><p>Preferências aplicadas a este navegador.</p><label>Idioma<select name="language" defaultValue="pt-BR"><option value="pt-BR">Português (Brasil)</option><option value="en">English</option></select></label><label>Página inicial<select name="home" defaultValue="dashboard"><option value="dashboard">Visão geral</option><option value="ocorrencias">Ocorrências</option></select></label></section><button className="primary-button" type="submit">Salvar alterações</button></form>;
}

function ProfileForm({ user, onSaved }: { user: TenantUser; onSaved: () => void }) {
  return <form className="settings-form profile-form" action={onSaved}><section><h2>Dados pessoais</h2><p>Os dados são demonstrativos até existir o endpoint de atualização.</p><label>Nome completo<input name="name" defaultValue={user.name}/></label><label>E-mail<input name="email" type="email" defaultValue={user.email}/></label><label>Cargo<input name="role" value={user.role_name ?? ""} readOnly/></label></section><button className="primary-button" type="submit">Salvar perfil</button><button className="secondary-button" formAction={logoutAction}>Sair da conta</button></form>;
}
