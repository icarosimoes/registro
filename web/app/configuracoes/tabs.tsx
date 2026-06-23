"use client";

import Link from "next/link";
import { useState } from "react";
import { Landmark, Plug, User } from "lucide-react";
import type { TenantUser } from "@/lib/api";
import { CompanySettingsSection, BrevoSettingsSection, EvolutionSettingsSection, ProfileForm } from "@/components/settings-sections";

const tabs = [
  { key: "estabelecimento", label: "Estabelecimento", icon: Landmark },
  { key: "integracoes", label: "Integrações", icon: Plug },
  { key: "conta", label: "Minha conta", icon: User },
] as const;

export function SettingsTabs({ activeTab, user }: { activeTab: string; user: TenantUser }) {
  const [toast, setToast] = useState("");

  function showToast(msg: string) {
    setToast(msg);
    setTimeout(() => setToast(""), 2600);
  }

  return (
    <>
      <nav style={{
        display: "flex", gap: "var(--sp-1)", padding: "0 var(--sp-5)",
        borderBottom: "1px solid var(--field-border)", marginBottom: "var(--sp-4)",
      }}>
        {tabs.map(({ key, label, icon: Icon }) => (
          <Link
            key={key}
            href={`/configuracoes?tab=${key}`}
            style={{
              display: "inline-flex", alignItems: "center", gap: 6,
              padding: "var(--sp-3) var(--sp-4)", fontSize: "var(--font-base)",
              fontWeight: activeTab === key ? 600 : 400,
              color: activeTab === key ? "var(--blue)" : "var(--label)",
              borderBottom: activeTab === key ? "2px solid var(--blue)" : "2px solid transparent",
              textDecoration: "none", transition: "color var(--transition)",
            }}
          >
            <Icon size={16} /> {label}
          </Link>
        ))}
      </nav>

      <section style={{ padding: "0 var(--sp-5)" }}>
        {activeTab === "estabelecimento" && <CompanySettingsSection />}
        {activeTab === "integracoes" && (
          <div className="settings-form">
            <BrevoSettingsSection />
            <EvolutionSettingsSection />
          </div>
        )}
        {activeTab === "conta" && <ProfileForm user={user} onSaved={showToast} />}
      </section>

      {toast && <div className="module-toast" role="status">{toast}</div>}
    </>
  );
}
