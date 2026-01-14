import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
  static values = { url: String }

  connect() {
    if (!this.hasUrlValue) return

    if (this.element.dataset.loaded) return

    this.renderLoading()
    this.load()
  }

  renderLoading() {
    this.element.innerHTML = `
      <div class="flex w-full h-36 items-center justify-center text-sm text-muted-foreground">Chargement...</div>
    `
  }

  async load() {
    try {
      const res = await fetch(this.urlValue, {
        method: 'POST',
        headers: { 'Accept': 'application/json, text/html' }
      })

      const result = await res.json()
      const data = result.data;

      if (!res.ok) {
          this.element.innerHTML = `
            <div class="flex h-36 items-center justify-center rounded-md border bg-red-50 text-sm text-red-700 p-2">${data}</div>
          `
        return
      }


      this.element.innerHTML = `
        <div class="flex items-center justify-center h-36 w-full text-2xl font-semibold">
            ${data}
        </div>
        `
      this.element.dataset.loaded = 'true'
    } catch (err) {
      console.error('widget-loader error', err)
      this.renderError()
    }
  }

  renderError(status = null) {
    this.element.innerHTML = `
      <div class="flex h-36 items-center justify-center rounded-md border bg-red-50 text-sm text-red-700 p-2">Erreur lors du chargement${status ? ' (' + status + ')' : ''}</div>
    `
  }
}
