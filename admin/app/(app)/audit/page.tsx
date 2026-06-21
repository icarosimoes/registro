import { platformFetch } from "@/lib/api";
import { fmtDate } from "@/lib/utils";

type AuditLog = {
  id: number;
  operator_email: string;
  action: string;
  entity_type: string;
  entity_id: number | null;
  details: Record<string, unknown> | null;
  created_at: string;
};

export default async function AuditPage() {
  let logs: AuditLog[] = [];
  try {
    logs = await platformFetch<AuditLog[]>("/platform/audit");
  } catch {
    logs = [];
  }

  return (
    <div className="space-y-6">
      <header>
        <h1 className="text-2xl font-bold text-gray-900">Auditoria</h1>
        <p className="text-sm text-gray-500">Ações administrativas na plataforma.</p>
      </header>

      <div className="rounded-xl border border-gray-100 overflow-hidden bg-white shadow-sm">
        <table className="w-full text-sm">
          <thead className="bg-gray-50 text-xs font-semibold text-gray-500 uppercase tracking-wide">
            <tr>
              <th className="px-4 py-3 text-left">Data</th>
              <th className="px-4 py-3 text-left">Operador</th>
              <th className="px-4 py-3 text-left">Ação</th>
              <th className="px-4 py-3 text-left">Entidade</th>
              <th className="px-4 py-3 text-left">Detalhes</th>
            </tr>
          </thead>
          <tbody>
            {logs.length === 0 && (
              <tr>
                <td colSpan={5} className="px-4 py-12 text-center text-gray-400">
                  Nenhum registro de auditoria.
                </td>
              </tr>
            )}
            {logs.map((log) => (
              <tr key={log.id} className="border-t border-gray-50 hover:bg-gray-50 transition-colors">
                <td className="px-4 py-3 text-xs text-gray-500 whitespace-nowrap">{fmtDate(log.created_at)}</td>
                <td className="px-4 py-3 text-gray-700">{log.operator_email}</td>
                <td className="px-4 py-3">
                  <span className="text-xs px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 font-medium">
                    {log.action}
                  </span>
                </td>
                <td className="px-4 py-3 text-xs text-gray-500">
                  {log.entity_type}
                  {log.entity_id ? ` #${log.entity_id}` : ""}
                </td>
                <td className="px-4 py-3 text-xs text-gray-400 max-w-xs truncate">
                  {log.details ? JSON.stringify(log.details) : "—"}
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}
