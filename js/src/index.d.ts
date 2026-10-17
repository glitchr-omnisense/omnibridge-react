import type { AnchorHTMLAttributes, ComponentType, ReactElement } from 'react';

export interface Page<P = Record<string, unknown>> {
  component: string;
  props: P;
  url: string;
  version: string | null;
}

export function usePage<P = Record<string, unknown>>(): Page<P>;
export function fetchPage<P = Record<string, unknown>>(url: string): Promise<Page<P>>;
export function visit<P = Record<string, unknown>>(url: string, options?: { replace?: boolean; preserveScroll?: boolean }): Promise<Page<P>>;
export function Link(props: AnchorHTMLAttributes<HTMLAnchorElement> & { href: string; replace?: boolean; preserveScroll?: boolean }): ReactElement;
export function createApp(options: { resolve: (name: string) => ComponentType<any> | undefined; el?: HTMLElement | null; prefix?: string }): void;
