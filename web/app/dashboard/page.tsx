import { DashboardShell } from "@/components/dashboard-shell";
import { currentTenantUser } from "@/lib/api";
import { redirect } from "next/navigation";

export default async function DashboardPage() {
  try {
    const user = await currentTenantUser();
    return <DashboardShell user={user} />;
  } catch (error) {
    if (error instanceof Error && error.message === "unauthorized") redirect("/login");
    throw error;
  }
}
