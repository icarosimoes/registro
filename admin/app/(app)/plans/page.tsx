import { platformFetch } from "@/lib/api";
import { brl } from "@/lib/utils";

type Plan = {
  id: number;
  code: string;
  name: string;
  price_cents: number;
  currency: string;
  billing_period: string;
  active: boolean;
  public: boolean;
  limits: Record<string, number>;
  features: Record<string, boolean>;
};

export default async function PlansPage() {
  let plans: Plan[] = [];
  try {
    plans = await platformFetch<Plan[]>("/platform/plans");
  } catch {
    plans = [];
  }

  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-bold text-gray-900">Planos</h1>
        <p className="text-sm text-gray-500">{plans.length} plano{plans.length !== 1 ? "s" : ""} configurado{plans.length !== 1 ? "s" : ""}</p>
      </header>

      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {plans.map((plan) => (
          <div key={plan.id} className="rounded-xl border border-gray-100 bg-white shadow-sm overflow-hidden">
            <div className="p-5">
              <div className="flex items-center justify-between">
                <h2 className="text-lg font-bold text-gray-900">{plan.name}</h2>
                <span className={`text-xs px-2 py-0.5 rounded-full font-medium ${plan.active ? "bg-emerald-100 text-emerald-700" : "bg-gray-100 text-gray-400"}`}>
                  {plan.active ? "Ativo" : "Inativo"}
                </span>
              </div>
              <p className="text-xs text-gray-400 font-mono mt-1">{plan.code}</p>
              <p className="text-2xl font-bold text-[#1D3461] mt-3">{brl(plan.price_cents)}</p>
              <p className="text-xs text-gray-400">
                {plan.billing_period === "monthly" ? "por mês" : plan.billing_period}
              </p>
            </div>
            {plan.limits && Object.keys(plan.limits).length > 0 && (
              <div className="border-t border-gray-100 px-5 py-3">
                <p className="text-[10px] font-semibold text-gray-400 uppercase tracking-wider mb-2">Limites</p>
                <div className="space-y-1">
                  {Object.entries(plan.limits).map(([key, val]) => (
                    <div key={key} className="flex justify-between text-xs">
                      <span className="text-gray-500">{key.replace(/_/g, " ")}</span>
                      <span className="font-medium text-gray-700">{val}</span>
                    </div>
                  ))}
                </div>
              </div>
            )}
          </div>
        ))}
        {plans.length === 0 && (
          <div className="col-span-full text-center py-12 text-gray-400">
            Nenhum plano configurado.
          </div>
        )}
      </div>
    </div>
  );
}
