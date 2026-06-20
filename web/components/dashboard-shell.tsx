"use client";

import {
  BellRing,
  ChevronRight,
  FileClock,
  MoreHorizontal,
  Plus,
  Receipt,
  Search,
  ShieldCheck,
  Users,
  X,
} from "lucide-react";
import { useMemo, useState } from "react";
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

type DashboardMetricsData = {
  open_occurrences: number;
  my_occurrences: number;
  open_fiscal: number;
  completed_month: number;
  active_users: number;
  active_sectors: number;
  recent: Array<{
    id: number;
    title: string;
    area: string;
    owner: string;
    status: string;
    updated_at: string;
  }>;
} | null;

function formatRelativeTime(dateStr: string): string {
  const date = new Date(dateStr);
  const now = new Date();
  const diffMs = now.getTime() - date.getTime();
  const diffMin = Math.floor(diffMs / 60_000);
  if (diffMin < 1) return "agora";
  if (diffMin < 60) return `há ${diffMin} min`;
  const diffH = Math.floor(diffMin / 60);
  if (diffH < 24) return `há ${diffH} h`;
  const diffD = Math.floor(diffH / 24);
  if (diffD === 1) return "ontem";
  return `há ${diffD} dias`;
}

function formatGreeting(): { greeting: string; dateLabel: string } {
  const now = new Date();
  const hour = now.getHours();
  const greeting = hour < 12 ? "Bom dia" : hour < 18 ? "Boa tarde" : "Boa noite";
  const dateLabel = now.toLocaleDateString("pt-BR", {
    weekday: "long",
    day: "numeric",
    month: "long",
  }).replace(/^\w/, (c) => c.toUpperCase());
  return { greeting, dateLabel };
}

const statusClass: Record<TicketStatus, string> = {
  "Em andamento": "status status-progress",
  Aguardando: "status status-waiting",
  Concluído: "status status-done",
};

export function DashboardShell({ user, metrics }: { user?: TenantUser; metrics?: DashboardMetricsData }) {
  const [query, setQuery] = useState("");
  const [scope, setScope] = useState<"all" | "mine" | "pending">("all");
  const [selectedTicket, setSelectedTicket] = useState<Ticket | null>(null);
  const displayName = user?.name ?? "Usuário";
  const firstName = displayName.split(" ")[0];

  const { greeting, dateLabel } = useMemo(formatGreeting, []);

  const tickets: Ticket[] = useMemo(() => {
    if (!metrics?.recent?.length) return [];
    return metrics.recent.map((item) => ({
      id: item.id,
      title: item.title,
      area: item.area,
      owner: item.owner,
      status: item.status as TicketStatus,
      updatedAt: formatRelativeTime(item.updated_at),
    }));
  }, [metrics]);

  const openOccurrences = metrics?.open_occurrences ?? 0;
  const myOccurrences = metrics?.my_occurrences ?? 0;
  const openFiscal = metrics?.open_fiscal ?? 0;
  const completedMonth = metrics?.completed_month ?? 0;
  const activeUsers = metrics?.active_users ?? 0;
  const activeSectors = metrics?.active_sectors ?? 0;

  const filteredTickets = useMemo(() => {
    const normalized = query.trim().toLocaleLowerCase("pt-BR");
    return tickets.filter((ticket) =>
      [String(ticket.id), ticket.title, ticket.area, ticket.owner, ticket.status]
        .join(" ")
        .toLocaleLowerCase("pt-BR")
        .includes(normalized) && (scope === "all" || (scope === "mine" ? ticket.owner.includes(firstName) : ticket.status !== "Concluído")),
    );
  }, [firstName, query, scope, tickets]);

  return (
    <>
      <div className="page-heading">
        <div>
          <div className="eyebrow">{dateLabel}</div>
          <h1>{greeting}, {firstName}</h1>
          <p>Acompanhe o que precisa de atenção na operação.</p>
        </div>
        <Link href="/ocorrencias?new=1" className="primary-button"><Plus size={18} /> Nova ocorrência</Link>
      </div>

      <section className="metrics-grid" aria-label="Indicadores principais">
        <article className="metric-card accent-blue"><span>Ocorrências abertas</span><strong>{openOccurrences}</strong><small>{myOccurrences} aguardam sua análise</small><FileClock /></article>
        <article className="metric-card accent-orange"><span>Solicitações fiscais</span><strong>{openFiscal}</strong><small>pendentes de resolução</small><Receipt /></article>
        <article className="metric-card accent-green"><span>Concluídos no mês</span><strong>{completedMonth}</strong><small>ocorrências finalizadas</small><ShieldCheck /></article>
        <article className="metric-card accent-purple"><span>Equipe ativa</span><strong>{activeUsers}</strong><small>{activeSectors} setores em operação</small><Users /></article>
      </section>

      <div className="content-grid">
        <section className="panel activity-panel">
          <div className="panel-heading">
            <div><h2>Atividades recentes</h2><p>Itens que exigem acompanhamento</p></div>
            <Link href="/ocorrencias" className="secondary-button">Ver todas <ChevronRight size={16} /></Link>
          </div>
          <div className="table-tools">
            <div className="segmented"><button className={scope === "all" ? "selected" : ""} onClick={() => setScope("all")}>Todas</button><button className={scope === "mine" ? "selected" : ""} onClick={() => setScope("mine")}>Minhas</button><button className={scope === "pending" ? "selected" : ""} onClick={() => setScope("pending")}>Pendentes</button></div>
            <label className="table-search">
              <Search size={16} />
              <input value={query} onChange={(e) => setQuery(e.target.value)} placeholder="Filtrar atividades…" aria-label="Filtrar atividades" />
            </label>
          </div>
          <div className="table-scroll">
            <table>
              <thead><tr><th>Protocolo</th><th>Atividade</th><th>Área</th><th>Responsável</th><th>Status</th><th>Atualização</th></tr></thead>
              <tbody>
                {filteredTickets.map((ticket) => (
                  <tr key={ticket.id} onClick={() => setSelectedTicket(ticket)}><td className="protocol">#{ticket.id}</td><td><strong>{ticket.title}</strong></td><td>{ticket.area}</td><td>{ticket.owner}</td><td><span className={statusClass[ticket.status] ?? "status status-progress"}>{ticket.status}</span></td><td className="muted">{ticket.updatedAt}</td></tr>
                ))}
              </tbody>
            </table>
            {!filteredTickets.length && <div className="empty-search"><Search size={28} /><strong>{metrics ? "Nenhuma atividade" : "Carregando..."}</strong><span>{metrics ? "Ainda não há ocorrências registradas." : "Conectando ao servidor..."}</span></div>}
          </div>
        </section>

        <aside className="right-column">
          <section className="panel progress-panel">
            <div className="panel-heading"><div><h2>Resumo</h2><p>{openOccurrences + openFiscal} itens abertos</p></div></div>
            <div className="summary-stats">
              <div><span>Ocorrências</span><strong>{openOccurrences}</strong></div>
              <div><span>Solicitações fiscais</span><strong>{openFiscal}</strong></div>
              <div><span>Concluídos (mês)</span><strong>{completedMonth}</strong></div>
            </div>
          </section>
          <section className="panel notices-panel">
            <div className="panel-heading"><div><h2>Mural de avisos</h2><p>Comunicados da equipe</p></div><BellRing size={19} /></div>
            <Link href="/mural" className="text-button">Abrir mural <ChevronRight size={15} /></Link>
          </section>
        </aside>
      </div>

      {selectedTicket ? <><button className="panel-backdrop" aria-label="Fechar detalhes" onClick={() => setSelectedTicket(null)}/><aside className="record-drawer"><header><div><span>Ocorrência #{selectedTicket.id}</span><h2>{selectedTicket.title}</h2></div><button className="icon-button" onClick={() => setSelectedTicket(null)}><X/></button></header><dl><div><dt>Status</dt><dd><span className={statusClass[selectedTicket.status] ?? "status status-progress"}>{selectedTicket.status}</span></dd></div><div><dt>Área</dt><dd>{selectedTicket.area}</dd></div><div><dt>Responsável</dt><dd>{selectedTicket.owner}</dd></div><div><dt>Atualização</dt><dd>{selectedTicket.updatedAt}</dd></div></dl><footer><Link href="/ocorrencias" className="primary-button">Abrir no módulo</Link></footer></aside></> : null}
    </>
  );
}
