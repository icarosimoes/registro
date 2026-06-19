import { logoutAction } from "@/app/actions";
import { platformFetch } from "@/lib/api";
import { redirect } from "next/navigation";

type Metrics = { tenants_total: number; tenants_active: number; tenants_trial: number; tenants_past_due: number; mrr_cents: number };
type Tenant = { id: number; name: string; slug: string; status: string; users_count: number; subscription_status: string | null; plan_name: string | null };
type Plan = { id: number; name: string; price_cents: number; currency: string; billing_period: string; active: boolean };

export default async function DashboardPage() {
  let data: [Metrics, Tenant[], Plan[]];
  try {
    data = await Promise.all([
      platformFetch<Metrics>("/platform/metrics"),
      platformFetch<Tenant[]>("/platform/tenants"),
      platformFetch<Plan[]>("/platform/plans"),
    ]);
  } catch (error) {
    if (error instanceof Error && error.message === "unauthorized") redirect("/login");
    throw error;
  }
  const [metrics, tenants, plans] = data;
  return (
    <main className="admin-shell">
      <header><div><span className="brand">REGISTRO</span><strong>Admin SaaS</strong></div><form action={logoutAction}><button className="ghost">Sair</button></form></header>
      <section className="heading"><div><p>Visão da plataforma</p><h1>Empresas e assinaturas</h1></div><span className="environment">Ambiente local</span></section>
      <section className="metrics">
        <article><span>Empresas</span><strong>{metrics.tenants_total}</strong><small>{metrics.tenants_active} ativas</small></article>
        <article><span>Em trial</span><strong>{metrics.tenants_trial}</strong><small>período de avaliação</small></article>
        <article><span>Inadimplentes</span><strong>{metrics.tenants_past_due}</strong><small>exigem acompanhamento</small></article>
        <article><span>MRR</span><strong>{(metrics.mrr_cents / 100).toLocaleString("pt-BR", { style: "currency", currency: "BRL" })}</strong><small>assinaturas ativas</small></article>
      </section>
      <section className="grid">
        <div className="panel"><div className="panel-title"><h2>Empresas</h2><span>{tenants.length}</span></div><table><thead><tr><th>Empresa</th><th>Usuários</th><th>Plano</th><th>Assinatura</th></tr></thead><tbody>{tenants.map((tenant) => <tr key={tenant.id}><td><strong>{tenant.name}</strong><small>{tenant.slug}</small></td><td>{tenant.users_count}</td><td>{tenant.plan_name ?? "—"}</td><td><span className={`badge ${tenant.subscription_status}`}>{tenant.subscription_status ?? "sem plano"}</span></td></tr>)}</tbody></table></div>
        <div className="panel plans"><div className="panel-title"><h2>Planos</h2><span>{plans.length}</span></div>{plans.map((plan) => <article key={plan.id}><div><strong>{plan.name}</strong><small>{plan.billing_period === "monthly" ? "Mensal" : plan.billing_period}</small></div><b>{(plan.price_cents / 100).toLocaleString("pt-BR", { style: "currency", currency: plan.currency })}</b></article>)}</div>
      </section>
    </main>
  );
}
