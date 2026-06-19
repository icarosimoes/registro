"use client";

import {
  Bell,
  BellRing,
  Building2,
  ChevronRight,
  ClipboardCheck,
  FileClock,
  FileText,
  HardHat,
  Home,
  LayoutDashboard,
  Menu,
  MessageSquareText,
  MoreHorizontal,
  PanelLeftClose,
  PanelLeftOpen,
  Plus,
  Search,
  Settings,
  ShieldCheck,
  Users,
  Wrench,
  X,
} from "lucide-react";
import { useMemo, useState } from "react";
import { logoutAction } from "@/app/actions";
import type { TenantUser } from "@/lib/api";
import Link from "next/link";

type TicketStatus = "Em andamento" | "Aguardando" | "Concluído";

type Ticket = {
  id: number;
  title: string;
  area: string;
  owner: string;
  status: TicketStatus;
  updatedAt: string;
};

const tickets: Ticket[] = [
  { id: 1048, title: "Revisar vistoria do apartamento 302", area: "Governança", owner: "Marina Costa", status: "Em andamento", updatedAt: "há 12 min" },
  { id: 1047, title: "Anexo pendente no diário de obra", area: "Engenharia", owner: "Rafael Lima", status: "Aguardando", updatedAt: "há 38 min" },
  { id: 1046, title: "Validar ocorrência do turno da manhã", area: "Operação", owner: "Ana Souza", status: "Em andamento", updatedAt: "há 1 h" },
  { id: 1045, title: "Ata da reunião semanal", area: "Administração", owner: "Carlos Reis", status: "Concluído", updatedAt: "ontem" },
];

const navigation = [
  { label: "Visão geral", icon: Home, active: true, href: "/dashboard" },
  { label: "Ocorrências", icon: FileClock, count: 7, href: "/ocorrencias" },
  { label: "Reuniões", icon: Users, href: "/reunioes" },
  { label: "Relatórios de turno", icon: FileText, href: "/relatorios-turno" },
  { label: "Inspeções", icon: ClipboardCheck, count: 3, href: "/inspecoes" },
  { label: "Diário de obra", icon: HardHat, href: "/diarios-obra" },
  { label: "Manutenção", icon: Wrench, href: "/manutencao" },
];

const statusClass: Record<TicketStatus, string> = {
  "Em andamento": "status status-progress",
  Aguardando: "status status-waiting",
  Concluído: "status status-done",
};

export function DashboardShell({ user }: { user?: TenantUser }) {
  const [collapsed, setCollapsed] = useState(false);
  const [mobileMenu, setMobileMenu] = useState(false);
  const [panel, setPanel] = useState<"notifications" | "profile" | null>(null);
  const [query, setQuery] = useState("");
  const [scope, setScope] = useState<"all" | "mine" | "pending">("all");
  const [selectedTicket, setSelectedTicket] = useState<Ticket | null>(null);
  const displayName = user?.name ?? "Ícaro Simoes";
  const firstName = displayName.split(" ")[0];
  const initials = displayName.split(" ").slice(0, 2).map((part) => part[0]).join("").toUpperCase();

  const filteredTickets = useMemo(() => {
    const normalized = query.trim().toLocaleLowerCase("pt-BR");
    return tickets.filter((ticket) =>
      [String(ticket.id), ticket.title, ticket.area, ticket.owner, ticket.status]
        .join(" ")
        .toLocaleLowerCase("pt-BR")
        .includes(normalized) && (scope === "all" || (scope === "mine" ? ticket.owner.includes(firstName) : ticket.status !== "Concluído")),
    );
  }, [firstName, query, scope]);

  function togglePanel(next: "notifications" | "profile") {
    setPanel((current) => (current === next ? null : next));
  }

  return (
    <div className={`app-shell ${collapsed ? "is-collapsed" : ""}`}>
      <aside className={`sidebar ${mobileMenu ? "is-open" : ""}`} aria-label="Navegação principal">
        <div className="brand-row">
          <div className="brand-mark" aria-hidden="true">A</div>
          {!collapsed && <div><strong>Registro</strong><span>Gestão operacional</span></div>}
          <button className="icon-button collapse-button" onClick={() => setCollapsed((value) => !value)} aria-label={collapsed ? "Expandir menu" : "Recolher menu"}>
            {collapsed ? <PanelLeftOpen size={18} /> : <PanelLeftClose size={18} />}
          </button>
        </div>

        <nav className="nav-list">
          <p className="nav-section">{collapsed ? "" : "Operação"}</p>
          {navigation.map(({ label, icon: Icon, active, count, href }) => (
            <Link href={href} className={`nav-item ${active ? "active" : ""}`} key={label} title={collapsed ? label : undefined}>
              <Icon size={19} aria-hidden="true" />
              {!collapsed && <span>{label}</span>}
              {!collapsed && count ? <small>{count}</small> : null}
            </Link>
          ))}
          <p className="nav-section">{collapsed ? "" : "Administração"}</p>
          <Link href="/cadastros" className="nav-item" title={collapsed ? "Cadastros" : undefined}><Building2 size={19} />{!collapsed && <span>Cadastros</span>}</Link>
          <Link href="/usuarios" className="nav-item" title={collapsed ? "Usuários e acesso" : undefined}><ShieldCheck size={19} />{!collapsed && <span>Usuários e acesso</span>}</Link>
        </nav>

        <div className="sidebar-footer">
          <Link href="/configuracoes" className="nav-item"><Settings size={19} />{!collapsed && <span>Configurações</span>}</Link>
          {!collapsed && <span className="version">Nova plataforma · prévia</span>}
        </div>
      </aside>

      {mobileMenu && <button className="backdrop" aria-label="Fechar menu" onClick={() => setMobileMenu(false)} />}

      <header className="topbar">
        <button className="icon-button mobile-menu" onClick={() => setMobileMenu(true)} aria-label="Abrir menu"><Menu size={21} /></button>
        <div className="workspace-tabs" aria-label="Áreas recentes">
          <Link href="/dashboard" className="workspace-tab active"><LayoutDashboard size={16} /> Visão geral</Link>
          <Link href="/ocorrencias" className="workspace-tab"><FileClock size={16} /> Ocorrências</Link>
          <Link href="/mural" className="new-tab" aria-label="Abrir mural"><Plus size={18} /></Link>
        </div>

        <label className="global-search">
          <Search size={18} aria-hidden="true" />
          <input value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Buscar protocolo, pessoa ou atividade" aria-label="Busca global" />
          <kbd>⌘ K</kbd>
        </label>

        <div className="top-actions">
          <button className="icon-button notification-button" onClick={() => togglePanel("notifications")} aria-label="Notificações"><Bell size={20} /><span>3</span></button>
          <button className="avatar-button" onClick={() => togglePanel("profile")} aria-label="Menu do usuário">{initials}</button>
        </div>
      </header>

      <main className="main-content">
        <div className="page-heading">
          <div>
            <div className="eyebrow">Sexta-feira, 19 de junho</div>
            <h1>Bom dia, {firstName}</h1>
            <p>Acompanhe o que precisa de atenção na operação.</p>
          </div>
          <Link href="/ocorrencias?new=1" className="primary-button"><Plus size={18} /> Nova ocorrência</Link>
        </div>

        <section className="metrics-grid" aria-label="Indicadores principais">
          <article className="metric-card accent-blue"><span>Ocorrências abertas</span><strong>12</strong><small>3 aguardam sua análise</small><FileClock /></article>
          <article className="metric-card accent-orange"><span>Inspeções pendentes</span><strong>7</strong><small>2 vencem hoje</small><ClipboardCheck /></article>
          <article className="metric-card accent-green"><span>Concluídos no mês</span><strong>84</strong><small>↑ 12% em relação a maio</small><ShieldCheck /></article>
          <article className="metric-card accent-purple"><span>Equipe ativa</span><strong>26</strong><small>4 setores em operação</small><Users /></article>
        </section>

        <div className="content-grid">
          <section className="panel activity-panel">
            <div className="panel-heading">
              <div><h2>Atividades recentes</h2><p>Itens que exigem acompanhamento</p></div>
              <Link href="/ocorrencias" className="secondary-button">Ver todas <ChevronRight size={16} /></Link>
            </div>
            <div className="table-tools">
              <div className="segmented"><button className={scope === "all" ? "selected" : ""} onClick={() => setScope("all")}>Todas</button><button className={scope === "mine" ? "selected" : ""} onClick={() => setScope("mine")}>Minhas</button><button className={scope === "pending" ? "selected" : ""} onClick={() => setScope("pending")}>Pendentes</button></div>
              <Link href="/ocorrencias" className="icon-button"><MoreHorizontal size={19} /><span className="sr-only">Mais opções</span></Link>
            </div>
            <div className="table-scroll">
              <table>
                <thead><tr><th>Protocolo</th><th>Atividade</th><th>Área</th><th>Responsável</th><th>Status</th><th>Atualização</th></tr></thead>
                <tbody>
                  {filteredTickets.map((ticket) => (
                    <tr key={ticket.id} onClick={() => setSelectedTicket(ticket)}><td className="protocol">#{ticket.id}</td><td><strong>{ticket.title}</strong></td><td>{ticket.area}</td><td>{ticket.owner}</td><td><span className={statusClass[ticket.status]}>{ticket.status}</span></td><td className="muted">{ticket.updatedAt}</td></tr>
                  ))}
                </tbody>
              </table>
              {!filteredTickets.length && <div className="empty-search"><Search size={28} /><strong>Nenhum resultado</strong><span>Tente buscar por outro termo.</span></div>}
            </div>
          </section>

          <aside className="right-column">
            <section className="panel progress-panel">
              <div className="panel-heading"><div><h2>Ritmo da semana</h2><p>37 de 48 atividades</p></div><strong>77%</strong></div>
              <div className="progress-track"><span /></div>
              <div className="progress-labels"><span>Seg</span><span>Ter</span><span>Qua</span><span>Qui</span><span>Sex</span></div>
              <div className="progress-bars" aria-label="Atividades por dia"><i style={{ height: "45%" }} /><i style={{ height: "70%" }} /><i style={{ height: "58%" }} /><i style={{ height: "90%" }} /><i className="today" style={{ height: "62%" }} /></div>
            </section>
            <section className="panel notices-panel">
              <div className="panel-heading"><div><h2>Mural de avisos</h2><p>Comunicados da equipe</p></div><BellRing size={19} /></div>
              <article><span>Operação</span><strong>Checklist de fechamento atualizado</strong><p>Confira as novas etapas antes de concluir o turno.</p></article>
              <article><span>Governança</span><strong>Inspeções da próxima semana</strong><p>A escala já está disponível para consulta.</p></article>
              <Link href="/mural" className="text-button">Abrir mural <ChevronRight size={15} /></Link>
            </section>
          </aside>
        </div>
      </main>

      {panel && (
        <>
          <button className="panel-backdrop" aria-label="Fechar painel" onClick={() => setPanel(null)} />
          <aside className="context-drawer" aria-label={panel === "notifications" ? "Notificações" : "Perfil"}>
            <div className="drawer-heading"><div><span>{panel === "notifications" ? "Central" : "Conta"}</span><h2>{panel === "notifications" ? "Notificações" : displayName}</h2></div><button className="icon-button" onClick={() => setPanel(null)} aria-label="Fechar"><X size={20} /></button></div>
            {panel === "notifications" ? (
              <div className="notification-list">
                <article><span className="notification-icon orange"><ClipboardCheck /></span><div><strong>2 inspeções vencem hoje</strong><p>Revise os responsáveis antes das 17h.</p><small>há 8 min</small></div></article>
                <article><span className="notification-icon blue"><MessageSquareText /></span><div><strong>Novo comentário na ocorrência #1048</strong><p>Marina marcou você em uma atualização.</p><small>há 24 min</small></div></article>
                <article><span className="notification-icon green"><FileText /></span><div><strong>Relatório concluído</strong><p>O PDF do turno está pronto para baixar.</p><small>há 1 h</small></div></article>
              </div>
            ) : (
              <div className="profile-content">
                <div className="profile-card"><div className="profile-avatar">{initials}</div><strong>{displayName}</strong><span>{user?.role_name ?? "Demonstração"}</span></div>
                <Link href="/minha-conta"><Users /> Minha conta</Link><Link href="/usuarios"><ShieldCheck /> Segurança e acesso</Link><Link href="/configuracoes"><Settings /> Preferências</Link>
                {user ? <form action={logoutAction}><button className="logout-button" type="submit">Sair da conta</button></form> : null}
              </div>
            )}
          </aside>
        </>
      )}
      {selectedTicket ? <><button className="panel-backdrop" aria-label="Fechar detalhes" onClick={() => setSelectedTicket(null)}/><aside className="record-drawer"><header><div><span>Ocorrência #{selectedTicket.id}</span><h2>{selectedTicket.title}</h2></div><button className="icon-button" onClick={() => setSelectedTicket(null)}><X/></button></header><dl><div><dt>Status</dt><dd><span className={statusClass[selectedTicket.status]}>{selectedTicket.status}</span></dd></div><div><dt>Área</dt><dd>{selectedTicket.area}</dd></div><div><dt>Responsável</dt><dd>{selectedTicket.owner}</dd></div><div><dt>Atualização</dt><dd>{selectedTicket.updatedAt}</dd></div></dl><footer><Link href="/ocorrencias" className="primary-button">Abrir no módulo</Link></footer></aside></> : null}
    </div>
  );
}
