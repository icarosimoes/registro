import { redirect } from "next/navigation";
import { getPlatformToken, platformFetch } from "@/lib/api";
import { TenantsClient } from "./tenants-client";

export type Tenant = {
  id: number;
  name: string;
  slug: string;
  email: string;
  document: string | null;
  timezone: string;
  status: string;
  users_count: number;
  subscription_status: string | null;
  plan_name: string | null;
  created_at: string | null;
};

export type Plan = { id: number; code: string; name: string; price_cents: number };

export default async function TenantsPage() {
  if (!(await getPlatformToken())) redirect("/login");

  const [tenants, plans] = await Promise.all([
    platformFetch<Tenant[]>("/platform/tenants").catch(() => [] as Tenant[]),
    platformFetch<Plan[]>("/platform/plans").catch(() => [] as Plan[]),
  ]);

  return <TenantsClient initialTenants={tenants} plans={plans} />;
}
