"use client";

import { useEffect, useRef, useState } from "react";
import {
  Ban, CheckCircle, MoreVertical, Pencil, Plus, Search, ShieldOff, Trash2, X,
} from "lucide-react";
import type { Tenant, Plan } from "./page";
import { fmtDate } from "@/lib/utils";

const STATUS_LABEL: Record<string, string> = {
  trial: "Trial", active: "Ativo", past_due: "Inadimplente",
  canceled: "Cancelado", suspended: "Suspenso",
};

const STATUS_CLASS: Record<string, string> = {
  trial: "bg-blue-100 text-blue-700", active: "bg-emerald-100 text-emerald-700",
  past_due: "bg-red-100 text-red-700", canceled: "bg-gray-100 text-gray-400",
  suspended: "bg-yellow-100 text-yellow-700",
};

const SUB_ACTIONS: Record<string, { label: string; nextStatus: string; icon: React.ReactNode; danger?: boolean }[]> = {
  trial:     [{ label: "Suspender", nextStatus: "suspended", icon: <ShieldOff className="h-3.5 w-3.5" />, danger: true },
              { label: "Cancelar",  nextStatus: "canceled",  icon: <Ban className="h-3.5 w-3.5" />, danger: true }],
  active:    [{ label: "Suspender", nextStatus: "suspended", icon: <ShieldOff className="h-3.5 w-3.5" />, danger: true },
              { label: "Cancelar",  nextStatus: "canceled",  icon: <Ban className="h-3.5 w-3.5" />, danger: true }],
  past_due:  [{ label: "Reativar",  nextStatus: "active",    icon: <CheckCircle className="h-3.5 w-3.5" /> },
              { label: "Suspender", nextStatus: "suspended", icon: <ShieldOff className="h-3.5 w-3.5" />, danger: true }],
  suspended: [{ label: "Reativar",  nextStatus: "active",    icon: <CheckCircle className="h-3.5 w-3.5" /> },
              { label: "Cancelar",  nextStatus: "canceled",  icon: <Ban className="h-3.5 w-3.5" />, danger: true }],
  canceled:  [{ label: "Reativar (trial)", nextStatus: "trial", icon: <CheckCircle className="h-3.5 w-3.5" /> }],
};

async function apiFetch<T>(path: string, init: RequestInit = {}): Promise<T> {
  const res = await fetch(`/api/proxy${path}`, {
    ...init,
    headers: { "Content-Type": "application/json", ...(init.headers ?? {}) },
  });
  if (!res.ok) throw new Error(await res.text());
  if (res.status === 204) return undefined as T;
  return res.json();
}

function SubscriptionMenu({ tenant, onUpdated }: { tenant: Tenant; onUpdated: (t: Tenant) => void }) {
  const [open, setOpen] = useState(false);
  const [loading, setLoading] = useState(false);
  const ref = useRef<HTMLDivElement>(null);
  const status = tenant.subscription_status ?? "";
  const actions = SUB_ACTIONS[status] ?? [];

  useEffect(() => {
    if (!open) return;
    function close(e: MouseEvent) {
      if (ref.current && !ref.current.contains(e.target as Node)) setOpen(false);
    }
    document.addEventListener("mousedown", close);
    return () => document.removeEventListener("mousedown", close);
  }, [open]);

  if (actions.length === 0) return null;

  async function apply(nextStatus: string, label: string) {
    if (!confirm(`${label} a assinatura de ${tenant.name}?`)) return;
    setLoading(true);
    setOpen(false);
    try {
      await apiFetch(`/tenants/${tenant.id}/subscription`, {
        method: "PATCH",
        body: JSON.stringify({ status: nextStatus }),
      });
      onUpdated({ ...tenant, subscription_status: nextStatus });
    } catch (err) {
      alert(err instanceof Error ? err.message : "Erro ao atualizar");
    } finally {
      setLoading(false);
    }
  }

  return (
    <div ref={ref} className="relative">
      <button
        onClick={() => setOpen((v) => !v)}
        disabled={loading}
        className="rounded-md p-1.5 text-gray-400 hover:bg-white hover:text-[#1D3461] disabled:opacity-50"
        title="Gerenciar assinatura"
      >
        <MoreVertical className="h-4 w-4" />
      </button>
      {open && (
        <div className="absolute right-0 top-8 z-50 w-48 rounded-xl border border-gray-100 bg-white shadow-xl py-1">
          <p className="px-3 py-1.5 text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Assinatura</p>
          {actions.map((a) => (
            <button
              key={a.nextStatus}
              onClick={() => apply(a.nextStatus, a.label)}
              className={`flex w-full items-center gap-2 px-3 py-2 text-sm hover:bg-gray-50 ${a.danger ? "text-red-600" : "text-emerald-700"}`}
            >
              {a.icon} {a.label}
            </button>
          ))}
        </div>
      )}
    </div>
  );
}

function NewTenantModal({ plans, onClose, onCreated }: { plans: Plan[]; onClose: () => void; onCreated: (t: Tenant) => void }) {
  const [form, setForm] = useState({ name: "", slug: "", email: "", plan_id: plans[0]?.id ?? 0 });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  function slugify(s: string) {
    return s.toLowerCase().normalize("NFD").replace(/[̀-ͯ]/g, "").replace(/[^a-z0-9]+/g, "-").replace(/^-|-$/g, "").slice(0, 30);
  }

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setError("");
    try {
      const t = await apiFetch<Tenant>("/tenants", { method: "POST", body: JSON.stringify(form) });
      onCreated(t);
      onClose();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao criar empresa");
    } finally {
      setLoading(false);
    }
  }

  const INPUT = "w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1D3461]/30 focus:border-[#1D3461]";
  const LABEL = "block text-xs font-medium text-gray-500 mb-1";

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
      <div className="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between" style={{ background: "linear-gradient(135deg, #1D3461, #142548)" }}>
          <div>
            <h2 className="text-lg font-bold text-white">Nova empresa</h2>
            <p className="text-xs text-white/60 mt-0.5">Cria tenant + assinatura trial</p>
          </div>
          <button onClick={onClose} className="text-white/60 hover:text-white"><X className="h-5 w-5" /></button>
        </div>
        <div className="p-6">
          {error && <p className="text-sm text-red-600 mb-3 p-3 bg-red-50 rounded-lg">{error}</p>}
          <form onSubmit={submit} className="space-y-3">
            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className={LABEL}>Nome da empresa</label>
                <input className={INPUT} value={form.name} onChange={(e) => setForm((f) => ({ ...f, name: e.target.value, slug: slugify(e.target.value) }))} required />
              </div>
              <div>
                <label className={LABEL}>Slug</label>
                <input className={INPUT} value={form.slug} onChange={(e) => setForm((f) => ({ ...f, slug: e.target.value }))} required />
              </div>
            </div>
            <div>
              <label className={LABEL}>E-mail do tenant</label>
              <input type="email" className={INPUT} value={form.email} onChange={(e) => setForm((f) => ({ ...f, email: e.target.value }))} required />
            </div>
            <div>
              <label className={LABEL}>Plano</label>
              <select className={INPUT} value={form.plan_id} onChange={(e) => setForm((f) => ({ ...f, plan_id: parseInt(e.target.value) }))}>
                <option value="0">Sem plano</option>
                {plans.map((p) => <option key={p.id} value={p.id}>{p.name}</option>)}
              </select>
            </div>
            <div className="flex justify-end gap-2 pt-2">
              <button type="button" onClick={onClose} className="px-4 py-2 text-sm rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">Cancelar</button>
              <button type="submit" disabled={loading} className="px-4 py-2 text-sm rounded-lg text-white font-medium disabled:opacity-50 transition-colors" style={{ background: "linear-gradient(135deg, #1D3461, #142548)" }}>
                {loading ? "Criando…" : "Criar empresa"}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}

function EditTenantModal({ tenant, onClose, onUpdated }: { tenant: Tenant; onClose: () => void; onUpdated: (t: Tenant) => void }) {
  const [form, setForm] = useState({
    name: tenant.name,
    email: tenant.email ?? "",
    document: tenant.document ?? "",
    timezone: tenant.timezone ?? "America/Sao_Paulo",
  });
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");

  async function submit(e: React.FormEvent) {
    e.preventDefault();
    setLoading(true);
    setError("");
    try {
      const body: Record<string, string> = {};
      if (form.name !== tenant.name) body.name = form.name;
      if (form.email !== (tenant.email ?? "")) body.email = form.email;
      if (form.document !== (tenant.document ?? "")) body.document = form.document;
      if (form.timezone !== (tenant.timezone ?? "America/Sao_Paulo")) body.timezone = form.timezone;
      if (Object.keys(body).length === 0) { onClose(); return; }
      await apiFetch(`/tenants/${tenant.id}`, { method: "PATCH", body: JSON.stringify(body) });
      onUpdated({ ...tenant, ...body });
      onClose();
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao atualizar");
    } finally {
      setLoading(false);
    }
  }

  const INPUT = "w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-[#1D3461]/30 focus:border-[#1D3461]";
  const LABEL = "block text-xs font-medium text-gray-500 mb-1";

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm p-4">
      <div className="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div className="px-6 py-4 border-b border-gray-100 flex items-center justify-between" style={{ background: "linear-gradient(135deg, #1D3461, #142548)" }}>
          <div>
            <h2 className="text-lg font-bold text-white">Editar empresa</h2>
            <p className="text-xs text-white/60 mt-0.5">{tenant.slug}</p>
          </div>
          <button onClick={onClose} className="text-white/60 hover:text-white"><X className="h-5 w-5" /></button>
        </div>
        <div className="p-6">
          {error && <p className="text-sm text-red-600 mb-3 p-3 bg-red-50 rounded-lg">{error}</p>}
          <form onSubmit={submit} className="space-y-3">
            <div>
              <label className={LABEL}>Nome da empresa</label>
              <input className={INPUT} value={form.name} onChange={(e) => setForm((f) => ({ ...f, name: e.target.value }))} required />
            </div>
            <div>
              <label className={LABEL}>E-mail</label>
              <input type="email" className={INPUT} value={form.email} onChange={(e) => setForm((f) => ({ ...f, email: e.target.value }))} placeholder="contato@hotel.com" />
            </div>
            <div className="grid grid-cols-2 gap-3">
              <div>
                <label className={LABEL}>CNPJ / CPF</label>
                <input className={INPUT} value={form.document} onChange={(e) => setForm((f) => ({ ...f, document: e.target.value }))} placeholder="00.000.000/0000-00" />
              </div>
              <div>
                <label className={LABEL}>Fuso horário</label>
                <select className={INPUT} value={form.timezone} onChange={(e) => setForm((f) => ({ ...f, timezone: e.target.value }))}>
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
                </select>
              </div>
            </div>
            <div className="flex justify-end gap-2 pt-2">
              <button type="button" onClick={onClose} className="px-4 py-2 text-sm rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors">Cancelar</button>
              <button type="submit" disabled={loading} className="px-4 py-2 text-sm rounded-lg text-white font-medium disabled:opacity-50 transition-colors" style={{ background: "linear-gradient(135deg, #1D3461, #142548)" }}>
                {loading ? "Salvando…" : "Salvar"}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  );
}

export function TenantsClient({ initialTenants, plans }: { initialTenants: Tenant[]; plans: Plan[] }) {
  const [tenants, setTenants] = useState<Tenant[]>(initialTenants);
  const [search, setSearch] = useState("");
  const [showModal, setShowModal] = useState(false);
  const [editing, setEditing] = useState<Tenant | null>(null);
  const [deleting, setDeleting] = useState<number | null>(null);
  const [error, setError] = useState("");

  const filtered = tenants.filter(
    (t) => t.name.toLowerCase().includes(search.toLowerCase()) || t.slug.toLowerCase().includes(search.toLowerCase()),
  );

  async function deleteTenant(tenant: Tenant) {
    if (!confirm(`Apagar a empresa ${tenant.name} (${tenant.slug})?`)) return;
    setDeleting(tenant.id);
    setError("");
    try {
      await apiFetch(`/tenants/${tenant.id}`, { method: "DELETE" });
      setTenants((prev) => prev.filter((item) => item.id !== tenant.id));
    } catch (err) {
      setError(err instanceof Error ? err.message : "Erro ao apagar");
    } finally {
      setDeleting(null);
    }
  }

  return (
    <div className="space-y-6">
      <header className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Empresas</h1>
          <p className="text-sm text-gray-500">{tenants.length} empresa{tenants.length !== 1 ? "s" : ""} registrada{tenants.length !== 1 ? "s" : ""}</p>
        </div>
        <button
          onClick={() => setShowModal(true)}
          className="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium text-white transition-colors"
          style={{ background: "linear-gradient(135deg, #1D3461, #142548)" }}
        >
          <Plus size={16} /> Nova empresa
        </button>
      </header>

      <div className="relative">
        <Search className="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" />
        <input
          className="w-full rounded-xl border border-gray-200 bg-white py-2.5 pl-9 pr-4 text-sm shadow-sm focus:outline-none focus:ring-2 focus:ring-[#1D3461]/30"
          placeholder="Buscar por nome ou slug…"
          value={search}
          onChange={(e) => setSearch(e.target.value)}
        />
      </div>

      {error && <p className="rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-600">{error}</p>}

      <div className="rounded-xl border border-gray-100 overflow-hidden bg-white shadow-sm">
        <table className="w-full text-sm">
          <thead className="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
            <tr>
              <th className="px-4 py-3 text-left">Empresa</th>
              <th className="px-4 py-3 text-left">Plano</th>
              <th className="px-4 py-3 text-left">Status</th>
              <th className="px-4 py-3 text-right">Usuários</th>
              <th className="px-4 py-3 text-right">Criado em</th>
              <th className="px-4 py-3 text-right">Ações</th>
            </tr>
          </thead>
          <tbody>
            {filtered.length === 0 && (
              <tr>
                <td colSpan={6} className="px-4 py-12 text-center text-gray-400">
                  {tenants.length === 0 ? "Nenhuma empresa registrada." : "Nenhum resultado para a busca."}
                </td>
              </tr>
            )}
            {filtered.map((t) => (
              <tr key={t.id} className="border-t border-gray-50 hover:bg-gray-50 transition-colors">
                <td className="px-4 py-3">
                  <p className="font-medium text-gray-900">{t.name}</p>
                  <p className="text-xs text-gray-400 font-mono">{t.slug}</p>
                </td>
                <td className="px-4 py-3">
                  {t.plan_name
                    ? <span className="text-xs font-medium text-gray-700">{t.plan_name}</span>
                    : <span className="text-xs text-gray-400">—</span>}
                </td>
                <td className="px-4 py-3">
                  {t.subscription_status
                    ? <span className={`text-xs px-2 py-0.5 rounded-full font-medium ${STATUS_CLASS[t.subscription_status] ?? "bg-gray-100 text-gray-500"}`}>
                        {STATUS_LABEL[t.subscription_status] ?? t.subscription_status}
                      </span>
                    : <span className="text-xs text-gray-400">sem plano</span>}
                </td>
                <td className="px-4 py-3 text-right text-gray-600">{t.users_count}</td>
                <td className="px-4 py-3 text-right text-xs text-gray-400">{fmtDate(t.created_at)}</td>
                <td className="px-4 py-3">
                  <div className="flex justify-end gap-1">
                    <button
                      onClick={() => setEditing(t)}
                      className="rounded-md p-1.5 text-gray-400 hover:bg-white hover:text-[#1D3461]"
                      title="Editar empresa"
                    >
                      <Pencil className="h-4 w-4" />
                    </button>
                    <SubscriptionMenu
                      tenant={t}
                      onUpdated={(updated) => setTenants((prev) => prev.map((x) => (x.id === updated.id ? updated : x)))}
                    />
                    <button
                      onClick={() => deleteTenant(t)}
                      disabled={deleting === t.id}
                      className="rounded-md p-1.5 text-gray-400 hover:bg-red-50 hover:text-red-600 disabled:opacity-50"
                      title="Apagar empresa"
                    >
                      <Trash2 className="h-4 w-4" />
                    </button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>

      {showModal && (
        <NewTenantModal
          plans={plans}
          onClose={() => setShowModal(false)}
          onCreated={(t) => setTenants((prev) => [t, ...prev])}
        />
      )}

      {editing && (
        <EditTenantModal
          tenant={editing}
          onClose={() => setEditing(null)}
          onUpdated={(updated) => {
            setTenants((prev) => prev.map((x) => (x.id === updated.id ? updated : x)));
            setEditing(null);
          }}
        />
      )}
    </div>
  );
}
