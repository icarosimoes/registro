"use client";

import { useEffect, useState } from "react";
import { Plus, Trash2 } from "lucide-react";
import {
  fetchWorkOrderCategories,
  addCategoryAction,
  deleteCategoryAction,
} from "@/app/actions";

export function CategoryManager() {
  const [categories, setCategories] = useState<string[]>([]);
  const [loading, setLoading] = useState(true);
  const [newName, setNewName] = useState("");
  const [saving, setSaving] = useState(false);
  const [toast, setToast] = useState("");

  function showToast(msg: string) {
    setToast(msg);
    setTimeout(() => setToast(""), 2600);
  }

  function reload() {
    fetchWorkOrderCategories().then(setCategories).finally(() => setLoading(false));
  }

  useEffect(() => { reload(); }, []);

  async function handleAdd(e: React.FormEvent) {
    e.preventDefault();
    const name = newName.trim();
    if (!name) return;
    if (categories.includes(name)) { showToast("Categoria já existe."); return; }
    setSaving(true);
    const result = await addCategoryAction(name);
    setSaving(false);
    if (result.ok) {
      setNewName("");
      showToast("Categoria criada.");
      reload();
    } else {
      showToast(result.error ?? "Erro ao criar.");
    }
  }

  async function handleDelete(name: string) {
    if (!confirm(`Excluir a categoria "${name}"? OS existentes com esta categoria não serão afetadas.`)) return;
    const result = await deleteCategoryAction(name);
    if (result.ok) {
      showToast("Categoria excluída.");
      reload();
    } else {
      showToast(result.error ?? "Erro ao excluir.");
    }
  }

  return (
    <section className="module-panel">
      <form onSubmit={handleAdd} style={{
        display: "flex", gap: "var(--sp-3)", padding: "var(--sp-4) var(--sp-5)",
        borderBottom: "1px solid var(--field-border)",
      }}>
        <input
          value={newName}
          onChange={(e) => setNewName(e.target.value)}
          placeholder="Nova categoria (ex: Elétrica, Hidráulica, HVAC...)"
          required
          style={{ flex: 1 }}
        />
        <button className="primary-button" type="submit" disabled={saving} style={{ display: "inline-flex", alignItems: "center", gap: 6 }}>
          <Plus size={16} /> {saving ? "Criando..." : "Adicionar"}
        </button>
      </form>

      {loading ? (
        <div className="module-state">Carregando categorias...</div>
      ) : categories.length === 0 ? (
        <div className="module-state">
          <strong>Nenhuma categoria cadastrada</strong>
          <span>Adicione categorias para organizar suas Ordens de Serviço.</span>
        </div>
      ) : (
        <div className="module-table-wrap">
          <table>
            <thead>
              <tr>
                <th>#</th>
                <th>Categoria</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              {categories.map((cat, i) => (
                <tr key={cat}>
                  <td className="protocol">#{i + 1}</td>
                  <td><strong>{cat}</strong></td>
                  <td>
                    <div className="row-actions">
                      <button onClick={() => handleDelete(cat)} aria-label="Excluir">
                        <Trash2 size={16} />
                      </button>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}

      <footer className="module-pagination">
        <span>{categories.length} categoria(s)</span>
      </footer>

      {toast && <div className="module-toast" role="status">{toast}</div>}
    </section>
  );
}
