"use client";

import Link from "next/link";
import { ClipboardCheck, CalendarCheck } from "lucide-react";

export function InspecoesTabBar({ activeTab }: { activeTab: "inspecoes" | "checklists" }) {
  return (
    <nav className="module-tabs" style={{
      display: "flex", gap: "var(--sp-1)", padding: "0 var(--sp-5)",
      borderBottom: "1px solid var(--field-border)", marginBottom: "var(--sp-4)",
    }}>
      <Link
        href="/inspecoes"
        className={activeTab === "inspecoes" ? "tab-active" : ""}
        style={{
          display: "inline-flex", alignItems: "center", gap: 6,
          padding: "var(--sp-3) var(--sp-4)", fontSize: "var(--font-base)",
          fontWeight: activeTab === "inspecoes" ? 600 : 400,
          color: activeTab === "inspecoes" ? "var(--blue)" : "var(--label)",
          borderBottom: activeTab === "inspecoes" ? "2px solid var(--blue)" : "2px solid transparent",
          textDecoration: "none", transition: "color var(--transition)",
        }}
      >
        <ClipboardCheck size={16} /> Inspeções
      </Link>
      <Link
        href="/inspecoes?tab=checklists"
        className={activeTab === "checklists" ? "tab-active" : ""}
        style={{
          display: "inline-flex", alignItems: "center", gap: 6,
          padding: "var(--sp-3) var(--sp-4)", fontSize: "var(--font-base)",
          fontWeight: activeTab === "checklists" ? 600 : 400,
          color: activeTab === "checklists" ? "var(--blue)" : "var(--label)",
          borderBottom: activeTab === "checklists" ? "2px solid var(--blue)" : "2px solid transparent",
          textDecoration: "none", transition: "color var(--transition)",
        }}
      >
        <CalendarCheck size={16} /> Checklists
      </Link>
    </nav>
  );
}
