import { AppLayout } from "@/components/app-layout";
import { RoleManager } from "@/components/role-manager";
import { currentTenantUser, tenantFetch } from "@/lib/api";
import { redirect } from "next/navigation";

type RoleItem = {
  id: number;
  code: string;
  name: string;
  permission_codes: string[];
  user_count: number;
  updated_at: string;
};
type RolePage = { items: RoleItem[]; total: number; page: number; page_size: number };

type PermissionItem = { id: number; code: string; name: string; module: string };
type PermissionGroup = { module: string; permissions: PermissionItem[] };

export default async function PerfisPage() {
  try {
    const user = await currentTenantUser();
    const [rolesData, permissionsData] = await Promise.all([
      tenantFetch<RolePage>("/roles?page=1&page_size=100"),
      tenantFetch<PermissionGroup[]>("/roles/permissions"),
    ]);
    return (
      <AppLayout user={user}>
        <RoleManager
          roles={rolesData.items}
          permissionGroups={permissionsData}
          user={user}
        />
      </AppLayout>
    );
  } catch (error) {
    if (error instanceof Error && error.message === "unauthorized") redirect("/login");
    throw error;
  }
}
