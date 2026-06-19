import { OperationalModule } from "@/components/operational-module";
import { currentTenantUser } from "@/lib/api";
import { moduleDefinitions } from "@/lib/module-definitions";
import { notFound, redirect } from "next/navigation";

export default async function ModulePage({ params }: { params: Promise<{ module: string }> }) {
  const { module } = await params;
  const definition = moduleDefinitions[module];
  if (!definition) notFound();

  try {
    const user = await currentTenantUser();
    return <OperationalModule definition={definition} user={user} />;
  } catch (error) {
    if (error instanceof Error && error.message === "unauthorized") redirect("/login");
    throw error;
  }
}
