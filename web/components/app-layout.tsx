"use client";

import {
  ArrowRightLeft, Bell, BookOpen, Building2, CalendarCheck, ClipboardCheck, ClipboardList,
  FileClock, FileText, HardHat, Home, Menu, MessageSquareText, Package,
  PanelLeftClose, PanelLeftOpen, Receipt,
  Settings, ShieldCheck, Timer, Users, Wrench, X,
} from "lucide-react";
import Link from "next/link";
import { usePathname } from "next/navigation";
import { useState, type ReactNode } from "react";
import { logoutAction } from "@/app/actions";
import type { TenantUser } from "@/lib/api";
import { NotificationPanel, NotificationBadge } from "./notification-panel";

const navOperation = [
  { slug: "dashboard", label: "Visão geral", icon: Home, href: "/dashboard" },
  { slug: "ocorrencias", label: "Ocorrências", icon: FileClock, href: "/ocorrencias" },
  { slug: "ordens-servico", label: "Ordens de Serviço", icon: ClipboardList, href: "/ordens-servico" },
  { slug: "reunioes", label: "Reuniões", icon: Users, href: "/reunioes" },
  { slug: "relatorios-turno", label: "Relatórios de turno", icon: FileText, href: "/relatorios-turno" },
  { slug: "inspecoes", label: "Inspeções", icon: ClipboardCheck, href: "/inspecoes" },
  { slug: "diarios-obra", label: "Diário de obra", icon: HardHat, href: "/diarios-obra" },
  { slug: "manutencao", label: "Manutenção", icon: Wrench, href: "/manutencao" },
  { slug: "preventivas", label: "Preventivas", icon: Timer, href: "/preventivas" },
  { slug: "checklists", label: "Checklists", icon: CalendarCheck, href: "/checklists" },
  { slug: "estoque", label: "Estoque", icon: Package, href: "/estoque" },
  { slug: "pendencias", label: "Pendências turno", icon: ArrowRightLeft, href: "/pendencias" },
  { slug: "solicitacoes-fiscais", label: "Solicitações Fiscais", icon: Receipt, href: "/solicitacoes-fiscais" },
];

const navAdmin = [
  { slug: "procedimentos", label: "Procedimentos", icon: BookOpen, href: "/procedimentos" },
  { slug: "cadastros", label: "Cadastros", icon: Building2, href: "/cadastros" },
  { slug: "usuarios", label: "Usuários e acesso", icon: ShieldCheck, href: "/usuarios" },
  { slug: "mural", label: "Mural de avisos", icon: Bell, href: "/mural" },
];

export function AppLayout({ user, children }: { user: TenantUser; children: ReactNode }) {
  const pathname = usePathname();
  const currentSlug = pathname === "/" || pathname === "/dashboard" ? "dashboard" : pathname.replace(/^\//, "").split("/")[0];
  const [collapsed, setCollapsed] = useState(false);
  const [mobileMenu, setMobileMenu] = useState(false);
  const [panel, setPanel] = useState<"notifications" | "profile" | null>(null);

  const displayName = user.name;
  const initials = displayName.split(" ").slice(0, 2).map((p) => p[0]).join("").toUpperCase();

  function togglePanel(next: "notifications" | "profile") {
    setPanel((c) => (c === next ? null : next));
  }

  return (
    <div className={`app-shell ${collapsed ? "is-collapsed" : ""}`}>
      <aside className={`sidebar ${mobileMenu ? "is-open" : ""}`} aria-label="Navegação principal">
        <div className="brand-row">
          <div className="brand-mark" aria-hidden="true">R</div>
          {!collapsed && <div><strong>Registro</strong><span>Gestão operacional</span></div>}
          <button className="icon-button collapse-button" onClick={() => setCollapsed((v) => !v)} aria-label={collapsed ? "Expandir menu" : "Recolher menu"}>
            {collapsed ? <PanelLeftOpen size={18} /> : <PanelLeftClose size={18} />}
          </button>
        </div>
        <nav className="nav-list">
          <p className="nav-section">{collapsed ? "" : "Operação"}</p>
          {navOperation.map(({ slug, label, icon: Icon, href }) => (
            <Link key={slug} href={href} className={`nav-item ${currentSlug === slug ? "active" : ""}`} title={collapsed ? label : undefined}>
              <Icon size={19} aria-hidden="true" />
              {!collapsed && <span>{label}</span>}
            </Link>
          ))}
          <p className="nav-section">{collapsed ? "" : "Administração"}</p>
          {navAdmin.map(({ slug, label, icon: Icon, href }) => (
            <Link key={slug} href={href} className={`nav-item ${currentSlug === slug ? "active" : ""}`} title={collapsed ? label : undefined}>
              <Icon size={19} aria-hidden="true" />
              {!collapsed && <span>{label}</span>}
            </Link>
          ))}
        </nav>
        <div className="sidebar-footer">
          <Link href="/configuracoes" className={`nav-item ${currentSlug === "configuracoes" ? "active" : ""}`}>
            <Settings size={19} />{!collapsed && <span>Configurações</span>}
          </Link>
          {!collapsed && <span className="version">Nova plataforma · prévia</span>}
        </div>
      </aside>

      {mobileMenu && <button className="backdrop" aria-label="Fechar menu" onClick={() => setMobileMenu(false)} />}

      <div className="top-float">
        <button className="icon-button mobile-menu-btn" onClick={() => setMobileMenu(true)} aria-label="Abrir menu"><Menu size={21} /></button>
        <div className="top-float-actions">
          <button className="icon-button notification-button" onClick={() => togglePanel("notifications")} aria-label="Notificações"><Bell size={20} /><NotificationBadge /></button>
          <button className="avatar-button" onClick={() => togglePanel("profile")} aria-label="Menu do usuário">{initials}</button>
        </div>
      </div>

      <main className="main-content">{children}</main>

      {panel && (
        <>
          <button className="panel-backdrop" aria-label="Fechar painel" onClick={() => setPanel(null)} />
          <aside className="context-drawer" aria-label={panel === "notifications" ? "Notificações" : "Perfil"}>
            <div className="drawer-heading">
              <div>
                <span>{panel === "notifications" ? "Central" : "Conta"}</span>
                <h2>{panel === "notifications" ? "Notificações" : displayName}</h2>
              </div>
              <button className="icon-button" onClick={() => setPanel(null)} aria-label="Fechar"><X size={20} /></button>
            </div>
            {panel === "notifications" ? (
              <NotificationPanel />
            ) : (
              <div className="profile-content">
                <div className="profile-card"><div className="profile-avatar">{initials}</div><strong>{displayName}</strong><span>{user.role_name ?? "Demonstração"}</span></div>
                <Link href="/minha-conta" onClick={() => setPanel(null)}><Users /> Minha conta</Link>
                <Link href="/usuarios" onClick={() => setPanel(null)}><ShieldCheck /> Segurança e acesso</Link>
                <Link href="/configuracoes" onClick={() => setPanel(null)}><Settings /> Preferências</Link>
                <form action={logoutAction}><button className="logout-button" type="submit">Sair da conta</button></form>
              </div>
            )}
          </aside>
        </>
      )}
    </div>
  );
}
