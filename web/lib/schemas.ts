import { z } from "zod";

export const TenantUserSchema = z.object({
  id: z.number(),
  name: z.string(),
  email: z.string(),
  phone: z.string().nullable(),
  company_id: z.number(),
  role_name: z.string().nullable(),
  permissions: z.array(z.string()),
});

export const TokenResponseSchema = z.object({
  access_token: z.string(),
  refresh_token: z.string(),
  expires_in: z.number(),
});

export const PaginatedSchema = <T extends z.ZodTypeAny>(itemSchema: T) =>
  z.object({
    items: z.array(itemSchema),
    total: z.number(),
    page: z.number(),
    page_size: z.number(),
  });

export const NotificationItemSchema = z.object({
  id: z.number(),
  title: z.string(),
  body: z.string().nullable(),
  category: z.string(),
  entity_type: z.string().nullable(),
  entity_id: z.number().nullable(),
  read_at: z.string().nullable(),
  created_at: z.string(),
});

export const NotificationListSchema = z.object({
  items: z.array(NotificationItemSchema),
  total: z.number(),
  unread: z.number(),
  page: z.number(),
  page_size: z.number(),
});

export const TimelineEntrySchema = z.object({
  id: z.number(),
  event_type: z.string(),
  user: z.string(),
  message: z.string().nullable(),
  changes: z.record(z.object({ from: z.string(), to: z.string() })).nullable(),
  created_at: z.string(),
});

export const AttachmentItemSchema = z.object({
  id: z.number(),
  entity_type: z.string(),
  entity_id: z.number(),
  filename: z.string(),
  content_type: z.string(),
  size_bytes: z.number(),
  uploaded_by_user_id: z.number(),
  created_at: z.string(),
});

export const RegistryOptionSchema = z.object({
  id: z.number(),
  name: z.string(),
});

export const UserOptionSchema = z.object({
  id: z.number(),
  name: z.string(),
  email: z.string(),
});

export function safeParse<T>(schema: z.ZodType<T>, data: unknown): T {
  const result = schema.safeParse(data);
  if (!result.success) {
    console.error("[API schema mismatch]", result.error.flatten());
    return data as T;
  }
  return result.data;
}
