"use client";

import { useEffect, useState } from "react";
import type { TenantUser } from "@/lib/api";
import type { EvolutionSettings, BrevoSettings, CompanyInfo } from "@/app/actions";
import {
  getEvolutionSettings, saveEvolutionSettings,
  getBrevoSettings, saveBrevoSettings,
} from "@/app/actions";

function formatPhone(v: string): string {
  const d = v.replace(/\D/g, "").slice(0, 11);
  if (d.length <= 2) return d.length ? `(${d}` : "";
  if (d.length <= 7) return `(${d.slice(0, 2)}) ${d.slice(2)}`;
  return `(${d.slice(0, 2)}) ${d.slice(2, 7)}-${d.slice(7)}`;
}

function formatDocument(v: string): string {
  const d = v.replace(/\D/g, "").slice(0, 14);
  if (d.length <= 3) return d;
  if (d.length <= 6) return `${d.slice(0, 3)}.${d.slice(3)}`;
  if (d.length <= 9) return `${d.slice(0, 3)}.${d.slice(3, 6)}.${d.slice(6)}`;
  if (d.length <= 11) return `${d.slice(0, 3)}.${d.slice(3, 6)}.${d.slice(6, 9)}-${d.slice(9)}`;
  if (d.length <= 12) return `${d.slice(0, 2)}.${d.slice(2, 5)}.${d.slice(5, 8)}/${d.slice(8)}`;
  return `${d.slice(0, 2)}.${d.slice(2, 5)}.${d.slice(5, 8)}/${d.slice(8, 12)}-${d.slice(12)}`;
}

export function CompanySettingsSection() {
  const [info, setInfo] = useState<CompanyInfo | null>(null);
  const [saving, setSaving] = useState(false);
  const [feedback, setFeedback] = useState<string | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    import("@/app/actions").then(({ getCompanyInfo }) =>
      getCompanyInfo().then((data) => { setInfo(data); setLoading(false); })
    ).catch(() => setLoading(false));
  }, []);

  if (loading) return <div className="settings-form"><section><h2>Estabelecimento</h2><p>Carregando...</p></section></div>;
  if (!info) return null;

  return <div className="settings-form"><form className="settings-evolution" onSubmit={async (e) => {
    e.preventDefault();
    setSaving(true);
    setFeedback(null);
    const fd = new FormData(e.currentTarget);
    const body: Record<string, string> = {};
    const name = String(fd.get("company_name") ?? "").trim();
    const email = String(fd.get("company_email") ?? "").trim();
    const document = String(fd.get("company_document") ?? "").trim();
    const timezone = String(fd.get("company_timezone") ?? "");
    if (name && name !== info.name) body.name = name;
    if (email !== (info.email ?? "")) body.email = email;
    if (document !== (info.document ?? "")) body.document = document;
    if (timezone && timezone !== info.timezone) body.timezone = timezone;
    if (!Object.keys(body).length) { setFeedback("Nenhum campo alterado."); setSaving(false); return; }
    const { updateCompanyInfo } = await import("@/app/actions");
    const result = await updateCompanyInfo(body);
    setSaving(false);
    if (result.ok) {
      setInfo({ ...info, ...body });
      setFeedback("Dados atualizados com sucesso.");
    } else {
      setFeedback(result.error ?? "Erro ao salvar.");
    }
  }}>
    <section>
      <h2>Estabelecimento</h2>
      <p>Dados cadastrais do seu hotel ou empresa.</p>
      {feedback && <p className={feedback.includes("sucesso") ? "settings-connected" : "settings-error"}>{feedback}</p>}
      <div className="form-grid">
        <label>Nome do estabelecimento<input name="company_name" type="text" required defaultValue={info.name}/></label>
        <label>E-mail corporativo<input name="company_email" type="email" placeholder="contato@hotel.com" defaultValue={info.email ?? ""}/></label>
      </div>
      <div className="form-grid">
        <label>CNPJ / CPF<input name="company_document" type="text" placeholder="00.000.000/0000-00" defaultValue={info.document ?? ""} onChange={(e) => { e.target.value = formatDocument(e.target.value); }}/></label>
        <label>Fuso horário<select name="company_timezone" defaultValue={info.timezone}>
          <option value="America/Sao_Paulo">Brasília (GMT-3)</option>
          <option value="America/Manaus">Manaus (GMT-4)</option>
          <option value="America/Belem">Belém (GMT-3)</option>
          <option value="America/Fortaleza">Fortaleza (GMT-3)</option>
          <option value="America/Recife">Recife (GMT-3)</option>
          <option value="America/Bahia">Salvador (GMT-3)</option>
          <option value="America/Cuiaba">Cuiabá (GMT-4)</option>
          <option value="America/Campo_Grande">Campo Grande (GMT-4)</option>
          <option value="America/Porto_Velho">Porto Velho (GMT-4)</option>
          <option value="America/Rio_Branco">Rio Branco (GMT-5)</option>
          <option value="America/Noronha">Fernando de Noronha (GMT-2)</option>
        </select></label>
      </div>
      <label>Identificador (slug)<input type="text" value={info.slug} readOnly/><small className="field-hint">O slug é gerado automaticamente e não pode ser alterado.</small></label>
    </section>
    <button className="primary-button" type="submit" disabled={saving}>{saving ? "Salvando..." : "Salvar dados"}</button>
  </form></div>;
}

export function BrevoSettingsSection() {
  const [config, setConfig] = useState<BrevoSettings | null>(null);
  const [saving, setSaving] = useState(false);
  const [feedback, setFeedback] = useState<string | null>(null);

  useEffect(() => {
    getBrevoSettings().then(setConfig).catch(() => setConfig({ has_credentials: false }));
  }, []);

  return <form className="settings-evolution" onSubmit={async (e) => {
    e.preventDefault();
    setSaving(true);
    setFeedback(null);
    const fd = new FormData(e.currentTarget);
    const result = await saveBrevoSettings({
      api_key: String(fd.get("brevo_api_key")),
      from_address: String(fd.get("brevo_from_address")),
      from_name: String(fd.get("brevo_from_name")),
    });
    setSaving(false);
    if (result.ok) {
      setConfig({ has_credentials: true, from_address: String(fd.get("brevo_from_address")), from_name: String(fd.get("brevo_from_name")) });
      setFeedback("Configuração salva com sucesso.");
    } else {
      setFeedback(result.error ?? "Erro ao salvar.");
    }
  }}>
    <section>
      <h2>E-mail (Brevo)</h2>
      <p>Configure o envio de e-mails transacionais para notificações de chamados e atualizações.</p>
      {config?.has_credentials && !feedback && <p className="settings-connected">Conectado{config.from_address ? ` — ${config.from_address}` : ""}</p>}
      {feedback && <p className={feedback.includes("sucesso") ? "settings-connected" : "settings-error"}>{feedback}</p>}
      <div className="form-grid">
        <label>E-mail remetente<input name="brevo_from_address" type="email" required placeholder="noreply@suaempresa.com" defaultValue={config?.from_address ?? ""}/></label>
        <label>Nome remetente<input name="brevo_from_name" type="text" required placeholder="Registro" defaultValue={config?.from_name ?? ""}/></label>
      </div>
      <label>API Key<input name="brevo_api_key" type="password" required placeholder={config?.has_credentials ? "Configurada — preencha para trocar" : "xkeysib-..."}/><small className="field-hint">Brevo → SMTP & API → API Keys</small></label>
    </section>
    <button className="primary-button" type="submit" disabled={saving}>{saving ? "Salvando..." : "Salvar e-mail"}</button>
  </form>;
}

export function EvolutionSettingsSection() {
  const [config, setConfig] = useState<EvolutionSettings | null>(null);
  const [saving, setSaving] = useState(false);
  const [feedback, setFeedback] = useState<string | null>(null);

  useEffect(() => {
    getEvolutionSettings().then(setConfig).catch(() => setConfig({ has_credentials: false }));
  }, []);

  return <form className="settings-evolution" onSubmit={async (e) => {
    e.preventDefault();
    setSaving(true);
    setFeedback(null);
    const fd = new FormData(e.currentTarget);
    const result = await saveEvolutionSettings({
      api_url: String(fd.get("evo_api_url")),
      api_key: String(fd.get("evo_api_key")),
      instance: String(fd.get("evo_instance")),
    });
    setSaving(false);
    if (result.ok) {
      setConfig({ has_credentials: true, api_url: String(fd.get("evo_api_url")), instance: String(fd.get("evo_instance")) });
      setFeedback("Configuração salva com sucesso.");
    } else {
      setFeedback(result.error ?? "Erro ao salvar.");
    }
  }}>
    <section>
      <h2>WhatsApp (Evolution API)</h2>
      <p>Configure a conexão com a Evolution API para enviar notificações via WhatsApp.</p>
      {config?.has_credentials && !feedback && <p className="settings-connected">Conectado{config.api_url ? ` — ${config.api_url}` : ""}</p>}
      {feedback && <p className={feedback.includes("sucesso") ? "settings-connected" : "settings-error"}>{feedback}</p>}
      <label>URL da instância<input name="evo_api_url" type="url" required placeholder="https://evo.suaempresa.com" defaultValue={config?.api_url ?? ""}/></label>
      <label>API Key<input name="evo_api_key" type="password" required placeholder="Chave de autenticação"/><small className="field-hint">Evolution → Manager → Global API Key ou API Key da instância</small></label>
      <label>Nome da instância<input name="evo_instance" type="text" required placeholder="aero-default" defaultValue={config?.instance ?? ""}/><small className="field-hint">Nome exato da instância criada no painel da Evolution API</small></label>
    </section>
    <button className="primary-button" type="submit" disabled={saving}>{saving ? "Salvando..." : "Salvar conexão"}</button>
  </form>;
}

export function ProfileForm({ user, onSaved }: { user: TenantUser; onSaved: (msg: string) => void }) {
  const [saving, setSaving] = useState(false);
  async function handleSubmit(e: React.FormEvent<HTMLFormElement>) {
    e.preventDefault();
    setSaving(true);
    const fd = new FormData(e.currentTarget);
    const name = String(fd.get("name") ?? "").trim();
    const phone = String(fd.get("phone") ?? "").replace(/\D/g, "") || undefined;
    const password = String(fd.get("password") ?? "") || undefined;
    const body: Record<string, string | undefined> = {};
    if (name && name !== user.name) body.name = name;
    if (phone) body.phone = phone;
    if (password) body.password = password;
    if (!Object.keys(body).length) { onSaved("Nenhum campo alterado."); setSaving(false); return; }
    const { updateProfileAction } = await import("@/app/actions");
    const result = await updateProfileAction(body);
    setSaving(false);
    if (result.ok) { onSaved("Perfil atualizado com sucesso."); } else { onSaved(result.error ?? "Erro ao salvar."); }
  }
  return <form className="settings-form profile-form" onSubmit={handleSubmit}>
    <section>
      <h2>Dados pessoais</h2>
      <label>Nome completo<input name="name" defaultValue={user.name} required/></label>
      <label>E-mail<input type="email" value={user.email} readOnly/><small className="field-hint">O e-mail não pode ser alterado por aqui.</small></label>
      <label>Telefone<input name="phone" type="tel" placeholder="(00) 00000-0000" defaultValue={user.phone ?? ""} onChange={(e) => { e.target.value = formatPhone(e.target.value); }}/></label>
      <label>Cargo<input value={user.role_name ?? ""} readOnly/></label>
      <label>Nova senha<small className="field-hint"> (deixe vazio para manter a atual)</small><input name="password" type="password" placeholder="••••••••"/></label>
    </section>
    <button className="primary-button" type="submit" disabled={saving}>{saving ? "Salvando..." : "Salvar perfil"}</button>
  </form>;
}
