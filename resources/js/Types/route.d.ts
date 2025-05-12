declare module 'vue' {
  interface ComponentCustomProperties {
    route: (name: string, params?: any, absolute?: boolean) => string;
  }
}
