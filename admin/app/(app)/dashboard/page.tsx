import { platformFetch } from "@/lib/api";
import { brl } from "@/lib/utils";

type Metrics = {
  tenants_total: number;
  tenants_active: number;
  tenants_trial: number;
  tenants_past_due: number;
  mrr_cents: number;
};

export default async function Dashboard() {
  let m: Metrics | null = null;
  try {
    m = await platformFetch<Metrics>("/platform/metrics");
  } catch {
    m = null;
  }

  const cards = [
    { label: "Empresas", value: m?.tenants_total ?? "—", sub: `${m?.tenants_active ?? 0} ativas`, color: "text-[#1D3461]" },
    { label: "Em trial", value: m?.tenants_trial ?? "—", sub: "período de avaliação", color: "text-blue-600" },
    { label: "Inadimplentes", value: m?.tenants_past_due ?? "—", sub: "exigem acompanhamento", color: "text-red-600" },
    { label: "MRR", value: m ? brl(m.mrr_cents) : "—", sub: "assinaturas ativas", color: "text-emerald-600" },
  ];

  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-bold text-gray-900">Dashboard</h1>
        <p className="text-sm text-gray-500">Visão geral da plataforma Registro.</p>
      </header>

      <section className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {cards.map((c) => (
          <div key={c.label} className="rounded-xl border border-gray-100 bg-white shadow-sm p-5">
            <h2 className="text-xs uppercase tracking-wider text-gray-400 font-semibold">{c.label}</h2>
            <p className={`mt-2 text-2xl font-bold ${c.color}`}>{c.value}</p>
            <p className="mt-1 text-xs text-gray-400">{c.sub}</p>
          </div>
        ))}
      </section>
    </div>
  );
}
