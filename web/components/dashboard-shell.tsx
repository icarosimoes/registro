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
  { label: "Visão geral", icon: Home, active: true },
  { label: "Ocorrências", icon: FileClock, count: 7 },
  { label: "Reuniões", icon: Users },
  { label: "Relatórios de turno", icon: FileText },
  { label: "Inspeções", icon: ClipboardCheck, count: 3 },
  { label: "Diário de obra", icon: HardHat },
  { label: "Manutenção", icon: Wrench },
];

const statusClass: Record<TicketStatus, string> = {
  "Em andamento": "status status-progress",
  Aguardando: "status status-waiting",
  Concluído: "status status-done",
};

export function DashboardShell() {
  const [collapsed, setCollapsed] = useState(false);
  const [mobileMenu, setMobileMenu] = useState(false);
  const [panel, setPanel] = useState<"notifications" | "profile" | null>(null);
  const [query, setQuery] = useState("");

  const filteredTickets = useMemo(() => {
    const normalized = query.trim().toLocaleLowerCase("pt-BR");
    if (!normalized) return tickets;
    return tickets.filter((ticket) =>
      [String(ticket.id), ticket.title, ticket.area, ticket.owner, ticket.status]
        .join(" ")
        .toLocaleLowerCase("pt-BR")
        .includes(normalized),
    );
  }, [query]);

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
          {navigation.map(({ label, icon: Icon, active, count }) => (
            <button className={`nav-item ${active ? "active" : ""}`} key={label} title={collapsed ? label : undefined}>
              <Icon size={19} aria-hidden="true" />
              {!collapsed && <span>{label}</span>}
              {!collapsed && count ? <small>{count}</small> : null}
            </button>
          ))}
          <p className="nav-section">{collapsed ? "" : "Administração"}</p>
          <button className="nav-item" title={collapsed ? "Cadastros" : undefined}><Building2 size={19} />{!collapsed && <span>Cadastros</span>}</button>
          <button className="nav-item" title={collapsed ? "Usuários e acesso" : undefined}><ShieldCheck size={19} />{!collapsed && <span>Usuários e acesso</span>}</button>
        </nav>

        <div className="sidebar-footer">
          <button className="nav-item"><Settings size={19} />{!collapsed && <span>Configurações</span>}</button>
          {!collapsed && <span className="version">Nova plataforma · prévia</span>}
        </div>
      </aside>

      {mobileMenu && <button className="backdrop" aria-label="Fechar menu" onClick={() => setMobileMenu(false)} />}

      <header className="topbar">
        <button className="icon-button mobile-menu" onClick={() => setMobileMenu(true)} aria-label="Abrir menu"><Menu size={21} /></button>
        <div className="workspace-tabs" aria-label="Áreas recentes">
          <button className="workspace-tab active"><LayoutDashboard size={16} /> Visão geral</button>
          <button className="workspace-tab"><FileClock size={16} /> Ocorrências</button>
          <button className="new-tab" aria-label="Abrir módulo"><Plus size={18} /></button>
        </div>

        <label className="global-search">
          <Search size={18} aria-hidden="true" />
          <input value={query} onChange={(event) => setQuery(event.target.value)} placeholder="Buscar protocolo, pessoa ou atividade" aria-label="Busca global" />
          <kbd>⌘ K</kbd>
        </label>

        <div className="top-actions">
          <button className="icon-button notification-button" onClick={() => togglePanel("notifications")} aria-label="Notificações"><Bell size={20} /><span>3</span></button>
          <button className="avatar-button" onClick={() => togglePanel("profile")} aria-label="Menu do usuário">IS</button>
        </div>
      </header>

      <main className="main-content">
        <div className="page-heading">
          <div>
            <div className="eyebrow">Sexta-feira, 19 de junho</div>
            <h1>Bom dia, Ícaro</h1>
            <p>Acompanhe o que precisa de atenção na operação.</p>
          </div>
          <button className="primary-button"><Plus size={18} /> Nova ocorrência</button>
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
              <button className="secondary-button">Ver todas <ChevronRight size={16} /></button>
            </div>
            <div className="table-tools">
              <div className="segmented"><button className="selected">Todas</button><button>Minhas</button><button>Pendentes</button></div>
              <button className="icon-button"><MoreHorizontal size={19} /><span className="sr-only">Mais opções</span></button>
            </div>
            <div className="table-scroll">
              <table>
                <thead><tr><th>Protocolo</th><th>Atividade</th><th>Área</th><th>Responsável</th><th>Status</th><th>Atualização</th></tr></thead>
                <tbody>
                  {filteredTickets.map((ticket) => (
                    <tr key={ticket.id}><td className="protocol">#{ticket.id}</td><td><strong>{ticket.title}</strong></td><td>{ticket.area}</td><td>{ticket.owner}</td><td><span className={statusClass[ticket.status]}>{ticket.status}</span></td><td className="muted">{ticket.updatedAt}</td></tr>
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
              <button className="text-button">Abrir mural <ChevronRight size={15} /></button>
            </section>
          </aside>
        </div>
      </main>

      {panel && (
        <>
          <button className="panel-backdrop" aria-label="Fechar painel" onClick={() => setPanel(null)} />
          <aside className="context-drawer" aria-label={panel === "notifications" ? "Notificações" : "Perfil"}>
            <div className="drawer-heading"><div><span>{panel === "notifications" ? "Central" : "Conta"}</span><h2>{panel === "notifications" ? "Notificações" : "Ícaro Simoes"}</h2></div><button className="icon-button" onClick={() => setPanel(null)} aria-label="Fechar"><X size={20} /></button></div>
            {panel === "notifications" ? (
              <div className="notification-list">
                <article><span className="notification-icon orange"><ClipboardCheck /></span><div><strong>2 inspeções vencem hoje</strong><p>Revise os responsáveis antes das 17h.</p><small>há 8 min</small></div></article>
                <article><span className="notification-icon blue"><MessageSquareText /></span><div><strong>Novo comentário na ocorrência #1048</strong><p>Marina marcou você em uma atualização.</p><small>há 24 min</small></div></article>
                <article><span className="notification-icon green"><FileText /></span><div><strong>Relatório concluído</strong><p>O PDF do turno está pronto para baixar.</p><small>há 1 h</small></div></article>
              </div>
            ) : (
              <div className="profile-content">
                <div className="profile-card"><div className="profile-avatar">IS</div><strong>Ícaro Santos Simoes</strong><span>Administrador</span></div>
                <button><Users /> Minha conta</button><button><ShieldCheck /> Segurança e acesso</button><button><Settings /> Preferências</button>
              </div>
            )}
          </aside>
        </>
      )}
    </div>
  );
}
