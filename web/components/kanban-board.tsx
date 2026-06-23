"use client";

import {
  createWorkOrderAction,
  deleteWorkOrderAction,
  fetchWorkOrderCategories,
  transitionWorkOrderAction,
  updateWorkOrderAction,
} from "@/app/actions";
import type { TenantUser } from "@/lib/api";
import type { ModuleDefinition, ModuleRecord } from "@/lib/module-definitions";
import { GripVertical, Plus, Search, Trash2, X } from "lucide-react";
import { useRouter } from "next/navigation";
import { useCallback, useEffect, useRef, useState, useTransition } from "react";

const KANBAN_COLUMNS = [
  { key: "aberta", label: "Aberta", color: "#3b82f6" },
  { key: "em_andamento", label: "Em andamento", color: "#f59e0b" },
  { key: "aguardando_material", label: "Aguardando material", color: "#8b5cf6" },
  { key: "concluida", label: "Concluída", color: "#10b981" },
  { key: "validada", label: "Validada", color: "#6b7280" },
];

const PRIORITIES = [
  { value: "urgente", label: "Urgente" },
  { value: "alta", label: "Alta" },
  { value: "media", label: "Média" },
  { value: "baixa", label: "Baixa" },
];

function priorityBadge(priority: string | undefined) {
  if (!priority) return null;
  const colors: Record<string, string> = {
    urgente: "#ef4444",
    alta: "#f97316",
    media: "#eab308",
    baixa: "#22c55e",
  };
  return (
    <span
      className="kanban-priority"
      style={{ "--priority-color": colors[priority] ?? "#94a3b8" } as React.CSSProperties}
    >
      {priority}
    </span>
  );
}

export function KanbanBoard({
  definition,
  user,
}: {
  definition: ModuleDefinition;
  user: TenantUser;
}) {
  const router = useRouter();
  const [query, setQuery] = useState("");
  const [showCreate, setShowCreate] = useState(false);
  const [editingRecord, setEditingRecord] = useState<ModuleRecord | null>(null);
  const [draggedId, setDraggedId] = useState<number | null>(null);
  const [dragOverColumn, setDragOverColumn] = useState<string | null>(null);
  const [error, setError] = useState<string | null>(null);
  const [isPending, startTransition] = useTransition();
  const dragSourceCol = useRef<string | null>(null);

  const records = definition.records;
  const canCreate = user.permissions.includes("*") || user.permissions.includes("work_order.create");
  const canEdit = user.permissions.includes("*") || user.permissions.includes("work_order.edit");
  const canDelete = user.permissions.includes("*") || user.permissions.includes("work_order.delete");

  const filtered = query
    ? records.filter(
        (r) =>
          r.title.toLowerCase().includes(query.toLowerCase()) ||
          r.owner.toLowerCase().includes(query.toLowerCase()),
      )
    : records;

  const grouped = new Map<string, ModuleRecord[]>();
  for (const col of KANBAN_COLUMNS) {
    grouped.set(col.key, []);
  }
  for (const record of filtered) {
    const list = grouped.get(record.status);
    if (list) list.push(record);
  }

  const handleDragStart = useCallback((e: React.DragEvent, recordId: number, sourceStatus: string) => {
    setDraggedId(recordId);
    dragSourceCol.current = sourceStatus;
    e.dataTransfer.effectAllowed = "move";
    e.dataTransfer.setData("text/plain", String(recordId));
  }, []);

  const handleDragOver = useCallback((e: React.DragEvent, colKey: string) => {
    e.preventDefault();
    e.dataTransfer.dropEffect = "move";
    setDragOverColumn(colKey);
  }, []);

  const handleDragLeave = useCallback(() => {
    setDragOverColumn(null);
  }, []);

  const handleDrop = useCallback((e: React.DragEvent, targetStatus: string) => {
    e.preventDefault();
    setDragOverColumn(null);
    const recordId = parseInt(e.dataTransfer.getData("text/plain"), 10);
    setDraggedId(null);

    if (!recordId || dragSourceCol.current === targetStatus) return;

    setError(null);
    startTransition(async () => {
      const result = await transitionWorkOrderAction(recordId, targetStatus);
      if (!result.ok) {
        setError(result.error ?? "Transição não permitida.");
        setTimeout(() => setError(null), 4000);
      }
      router.refresh();
    });
  }, [router]);

  const handleDelete = useCallback((id: number, title: string) => {
    if (!confirm(`Excluir a OS "${title}"?`)) return;
    setError(null);
    startTransition(async () => {
      const result = await deleteWorkOrderAction(id);
      if (!result.ok) {
        setError(result.error ?? "Erro ao excluir.");
      }
      router.refresh();
    });
  }, [router]);

  return (
    <>
      <header className="module-heading">
        <div>
          <p className="eyebrow">Operação</p>
          <h1>{definition.title}</h1>
          <p>{definition.description}</p>
        </div>
        {canCreate && (
          <button className="btn-primary" onClick={() => setShowCreate(true)}>
            <Plus size={18} />
            Nova OS
          </button>
        )}
      </header>

      {error && (
        <div className="kanban-error">
          {error}
          <button onClick={() => setError(null)} aria-label="Fechar"><X size={14} /></button>
        </div>
      )}

      <div className="kanban-toolbar">
        <label className="kanban-search">
          <Search size={18} />
          <input
            value={query}
            onChange={(e) => setQuery(e.target.value)}
            placeholder="Buscar ordens de serviço…"
          />
        </label>
        <span className="kanban-count">{filtered.length} ordem(ns)</span>
      </div>

      <div className="kanban-container">
        {KANBAN_COLUMNS.map((col) => {
          const items = grouped.get(col.key) ?? [];
          const isOver = dragOverColumn === col.key && dragSourceCol.current !== col.key;
          return (
            <div
              key={col.key}
              className={`kanban-column${isOver ? " kanban-column-dragover" : ""}`}
              onDragOver={(e) => handleDragOver(e, col.key)}
              onDragLeave={handleDragLeave}
              onDrop={(e) => handleDrop(e, col.key)}
            >
              <div className="kanban-column-header" style={{ "--col-color": col.color } as React.CSSProperties}>
                <span className="kanban-column-title">{col.label}</span>
                <span className="kanban-column-count">{items.length}</span>
              </div>
              <div className="kanban-column-body">
                {items.map((record) => (
                  <article
                    key={record.id}
                    className={`kanban-card${draggedId === record.id ? " kanban-card-dragging" : ""}`}
                    draggable={canEdit}
                    onDragStart={(e) => handleDragStart(e, record.id, col.key)}
                    onDragEnd={() => { setDraggedId(null); setDragOverColumn(null); }}
                    onClick={() => canEdit ? setEditingRecord(record) : undefined}
                  >
                    <div className="kanban-card-header">
                      <span className="kanban-card-id">
                        {canEdit && <GripVertical size={12} className="kanban-grip" />}
                        #{record.id}
                      </span>
                      <div style={{ display: "flex", alignItems: "center", gap: 6 }}>
                        {priorityBadge(record.priority)}
                        {canDelete && (
                          <button
                            className="kanban-card-delete"
                            onClick={(e) => { e.stopPropagation(); handleDelete(record.id, record.title); }}
                            aria-label="Excluir"
                          >
                            <Trash2 size={13} />
                          </button>
                        )}
                      </div>
                    </div>
                    <h3 className="kanban-card-title">{record.title}</h3>
                    {record.category && record.category !== "Geral" && (
                      <span className="kanban-card-category">{record.category}</span>
                    )}
                    <footer className="kanban-card-footer">
                      <span>{record.owner}</span>
                      <span>{record.updatedAt}</span>
                    </footer>
                    {record.slaDeadline && (
                      <div className="kanban-card-sla">
                        SLA: {new Intl.DateTimeFormat("pt-BR", { dateStyle: "short", timeStyle: "short" }).format(new Date(record.slaDeadline))}
                      </div>
                    )}
                  </article>
                ))}
                {items.length === 0 && (
                  <div className="kanban-empty">Nenhuma OS</div>
                )}
              </div>
            </div>
          );
        })}
      </div>

      {isPending && <div className="kanban-loading">Salvando…</div>}

      {showCreate && (
        <CreateWorkOrderModal
          onClose={() => setShowCreate(false)}
          onCreated={() => { setShowCreate(false); router.refresh(); }}
        />
      )}

      {editingRecord && (
        <EditWorkOrderModal
          record={editingRecord}
          onClose={() => setEditingRecord(null)}
          onSaved={() => { setEditingRecord(null); router.refresh(); }}
        />
      )}
    </>
  );
}

function useCategoryOptions() {
  const [categories, setCategories] = useState<string[]>([]);
  useEffect(() => {
    fetchWorkOrderCategories().then(setCategories);
  }, []);
  return categories;
}

function CategorySelect({ value, onChange, categories }: {
  value: string;
  onChange: (v: string) => void;
  categories: string[];
}) {
  const [custom, setCustom] = useState(false);

  if (custom) {
    return (
      <div style={{ display: "flex", gap: "var(--sp-2)" }}>
        <input
          type="text"
          value={value}
          onChange={(e) => onChange(e.target.value)}
          placeholder="Nova categoria..."
          autoFocus
          style={{ flex: 1 }}
        />
        <button type="button" onClick={() => { setCustom(false); onChange(""); }}
          style={{ fontSize: "var(--font-sm)", color: "var(--blue)", background: "none", border: "none", cursor: "pointer", whiteSpace: "nowrap" }}>
          Voltar
        </button>
      </div>
    );
  }

  return (
    <select value={value} onChange={(e) => {
      if (e.target.value === "__new__") { setCustom(true); onChange(""); }
      else onChange(e.target.value);
    }}>
      <option value="">Sem categoria</option>
      {categories.map((c) => <option key={c} value={c}>{c}</option>)}
      <option value="__new__">+ Nova categoria</option>
    </select>
  );
}

function CreateWorkOrderModal({
  onClose,
  onCreated,
}: {
  onClose: () => void;
  onCreated: () => void;
}) {
  const [title, setTitle] = useState("");
  const [description, setDescription] = useState("");
  const [priority, setPriority] = useState("");
  const [category, setCategory] = useState("");
  const [slaHours, setSlaHours] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [isPending, startTransition] = useTransition();
  const categories = useCategoryOptions();

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!title.trim()) return;

    startTransition(async () => {
      const result = await createWorkOrderAction({
        title: title.trim(),
        description: description.trim() || undefined,
        priority: priority || undefined,
        category: category.trim() || undefined,
        sla_hours: slaHours ? parseInt(slaHours, 10) : undefined,
      });
      if (!result.ok) {
        setError(result.error ?? "Erro ao criar OS.");
        return;
      }
      onCreated();
    });
  };

  return (
    <div className="modal-layer" onClick={onClose}>
      <div className="module-panel kanban-create-modal" onClick={(e) => e.stopPropagation()}>
        <header className="kanban-modal-header">
          <h2>Nova Ordem de Serviço</h2>
          <button onClick={onClose} aria-label="Fechar"><X size={20} /></button>
        </header>
        <form onSubmit={handleSubmit} className="kanban-create-form">
          {error && <div className="kanban-form-error">{error}</div>}
          <label>
            Título *
            <input
              type="text"
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              placeholder="Descreva a ordem de serviço"
              required
              autoFocus
            />
          </label>
          <label>
            Descrição
            <textarea
              value={description}
              onChange={(e) => setDescription(e.target.value)}
              placeholder="Detalhes adicionais (opcional)"
              rows={3}
            />
          </label>
          <div className="form-grid">
            <label>
              Prioridade
              <select value={priority} onChange={(e) => setPriority(e.target.value)}>
                <option value="">Sem prioridade</option>
                {PRIORITIES.map((p) => (
                  <option key={p.value} value={p.value}>{p.label}</option>
                ))}
              </select>
            </label>
            <label>
              Categoria
              <CategorySelect value={category} onChange={setCategory} categories={categories} />
            </label>
            <label>
              SLA (horas)
              <input
                type="number"
                value={slaHours}
                onChange={(e) => setSlaHours(e.target.value)}
                placeholder="Ex: 24"
                min={1}
              />
            </label>
          </div>
          <footer>
            <button type="button" onClick={onClose}>Cancelar</button>
            <button type="submit" disabled={isPending || !title.trim()}>
              {isPending ? "Criando…" : "Criar OS"}
            </button>
          </footer>
        </form>
      </div>
    </div>
  );
}

function EditWorkOrderModal({
  record,
  onClose,
  onSaved,
}: {
  record: ModuleRecord;
  onClose: () => void;
  onSaved: () => void;
}) {
  const [title, setTitle] = useState(record.title);
  const [description, setDescription] = useState(record.description ?? "");
  const [priority, setPriority] = useState(record.priority ?? "");
  const [category, setCategory] = useState(record.category === "Geral" ? "" : record.category);
  const [error, setError] = useState<string | null>(null);
  const [isPending, startTransition] = useTransition();
  const categories = useCategoryOptions();

  const handleSubmit = (e: React.FormEvent) => {
    e.preventDefault();
    if (!title.trim()) return;

    startTransition(async () => {
      const result = await updateWorkOrderAction(record.id, {
        title: title.trim(),
        description: description.trim() || undefined,
        priority: priority || undefined,
        category: category.trim() || undefined,
      });
      if (!result.ok) {
        setError(result.error ?? "Erro ao atualizar OS.");
        return;
      }
      onSaved();
    });
  };

  return (
    <div className="modal-layer" onClick={onClose}>
      <div className="module-panel kanban-create-modal" onClick={(e) => e.stopPropagation()}>
        <header className="kanban-modal-header">
          <h2>Editar OS #{record.id}</h2>
          <button onClick={onClose} aria-label="Fechar"><X size={20} /></button>
        </header>
        <form onSubmit={handleSubmit} className="kanban-create-form">
          {error && <div className="kanban-form-error">{error}</div>}
          <label>
            Título *
            <input
              type="text"
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              required
              autoFocus
            />
          </label>
          <label>
            Descrição
            <textarea
              value={description}
              onChange={(e) => setDescription(e.target.value)}
              rows={3}
            />
          </label>
          <div className="form-grid">
            <label>
              Prioridade
              <select value={priority} onChange={(e) => setPriority(e.target.value)}>
                <option value="">Sem prioridade</option>
                {PRIORITIES.map((p) => (
                  <option key={p.value} value={p.value}>{p.label}</option>
                ))}
              </select>
            </label>
            <label>
              Categoria
              <CategorySelect value={category} onChange={setCategory} categories={categories} />
            </label>
          </div>
          <footer>
            <button type="button" onClick={onClose}>Cancelar</button>
            <button type="submit" disabled={isPending || !title.trim()}>
              {isPending ? "Salvando…" : "Salvar"}
            </button>
          </footer>
        </form>
      </div>
    </div>
  );
}
