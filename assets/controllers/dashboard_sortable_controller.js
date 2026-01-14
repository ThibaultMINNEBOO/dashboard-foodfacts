import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
  static values = { url: String }

  connect() {
    this.dragged = null
    this.placeholder = null
    this.items = Array.from(this.element.querySelectorAll('[data-id]'))

    this.items.forEach(item => {
      item.setAttribute('draggable', 'true')
      const handlers = {
        dragstart: this.onDragStart.bind(this),
        drop: this.onDrop.bind(this),
        dragend: this.onDragEnd.bind(this),
      }
      item.__dragHandlers = handlers
      item.addEventListener('dragstart', handlers.dragstart)
      item.addEventListener('drop', handlers.drop)
      item.addEventListener('dragend', handlers.dragend)
    })

    // handle leaving the whole container: clear highlights
    this._onContainerDragLeave = (e) => {
      const related = e.relatedTarget
      if (!related || !this.element.contains(related)) {
        this.items.forEach(i => {
          const card = i.firstElementChild
          if (card) {
            if (card.dataset && card.dataset._prevStyle !== undefined) {
              card.setAttribute('style', card.dataset._prevStyle || '')
              delete card.dataset._prevStyle
            } else {
              card.style.transform = ''
              card.style.boxShadow = ''
              card.style.zIndex = ''
              card.style.backgroundColor = ''
              card.classList.remove('ring-4', 'ring-primary/40', 'bg-primary/5')
            }
          }
        })
        this.hoverTarget = null
      }
    }
    this.element.addEventListener('dragleave', this._onContainerDragLeave)

    // Add a container-level dragover to compute hovered item via elementFromPoint (robust over gaps/children)
    this._onContainerDragOver = (e) => {
      e.preventDefault()
      if (!this.dragged) return
      const el = document.elementFromPoint(e.clientX, e.clientY)
      if (!el) return
      const item = el.closest ? el.closest('[data-id]') : null
      if (!item || item === this.dragged) {
        this.items.forEach(i => {
          const c = i.firstElementChild
          if (c) {
            if (c.dataset && c.dataset._prevStyle !== undefined) {
              c.setAttribute('style', c.dataset._prevStyle || '')
              delete c.dataset._prevStyle
            } else {
              c.style.transform = ''
              c.style.boxShadow = ''
              c.style.zIndex = ''
              c.style.backgroundColor = ''
              c.classList.remove('ring-4', 'ring-primary/40', 'bg-primary/5')
            }
          }
        })
        this.hoverTarget = null
        return
      }

      const rect = item.getBoundingClientRect()

      this.items.forEach(i => {
        if (i === item) return
        const c = i.firstElementChild
        if (c) {
          if (c.dataset && c.dataset._prevStyle !== undefined) {
            c.setAttribute('style', c.dataset._prevStyle || '')
            delete c.dataset._prevStyle
          } else {
            c.style.transform = ''
            c.style.boxShadow = ''
            c.style.zIndex = ''
            c.style.backgroundColor = ''
            c.classList.remove('ring-4', 'ring-primary/40', 'bg-primary/5')
          }
        }
      })
      this.hoverTarget = item
      const card = item.firstElementChild
      if (card) {
        if (!card.dataset._prevStyle) card.dataset._prevStyle = card.getAttribute('style') || ''
        card.style.transition = 'transform .15s ease, box-shadow .15s ease, background-color .15s ease'
        card.style.transform = 'scale(1.05)'
        card.style.boxShadow = '0 12px 28px rgba(0,0,0,0.12), 0 0 0 4px rgba(59,130,246,0.25)'
        card.style.backgroundColor = ''
        card.style.zIndex = '15'
      }
     }
     document.addEventListener('dragover', this._onContainerDragOver)

    this._onDocumentDragEnd = () => { this.cleanupAfterDrop() }
    document.addEventListener('dragend', this._onDocumentDragEnd)

    try {
      const img = new Image()
      img.src = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw=='
      this._dragImage = img
    } catch (e) {
      this._dragImage = null
    }
  }

  disconnect() {
    this.items.forEach(item => {
      item.removeAttribute('draggable')
      const handlers = item.__dragHandlers
      if (handlers) {
        item.removeEventListener('dragstart', handlers.dragstart)
        item.removeEventListener('drop', handlers.drop)
        item.removeEventListener('dragend', handlers.dragend)
        delete item.__dragHandlers
      }
    })
    if (this._onContainerDragLeave) {
      this.element.removeEventListener('dragleave', this._onContainerDragLeave)
      this._onContainerDragLeave = null
    }
    if (this._onContainerDragOver) {
      document.removeEventListener('dragover', this._onContainerDragOver)
      this._onContainerDragOver = null
    }
    if (this._onDocumentDragEnd) {
      document.removeEventListener('dragend', this._onDocumentDragEnd)
      this._onDocumentDragEnd = null
    }
  }

  onDragStart(e) {
    const item = e.currentTarget
    this.dragged = item

    try { e.dataTransfer.setData('text/plain', item.dataset.id) } catch (err) {}
    e.dataTransfer.effectAllowed = 'move'

    if (this._dragImage && e.dataTransfer.setDragImage) {
      e.dataTransfer.setDragImage(this._dragImage, 0, 0)
    }

    const card = item.firstElementChild
    if (card) {
      card.dataset._prevStyle = card.getAttribute('style') || ''
      card.style.transition = 'transform .15s ease, box-shadow .15s ease, background-color .15s ease'
      card.style.transform = 'scale(0.98)'
      card.style.boxShadow = '0 8px 20px rgba(0,0,0,0.12)'
      card.style.zIndex = '20'
    } else {
      item.dataset._prevStyle = item.getAttribute('style') || ''
      item.style.transition = 'transform .15s ease, box-shadow .15s ease, background-color .15s ease'
      item.style.transform = 'scale(0.98)'
      item.style.boxShadow = '0 8px 20px rgba(0,0,0,0.12)'
      item.style.zIndex = '20'
    }

    this.hoverTarget = null
  }

  onDrop(e) {
    e.preventDefault()
    if (!this.dragged) return

    if (this.hoverTarget && this.hoverTarget.parentNode) {
      const a = this.dragged
      const b = this.hoverTarget
      const aParent = a.parentNode
      const bParent = b.parentNode

      const aNext = a.nextSibling
      const bNext = b.nextSibling

      if (aParent === bParent) {
        if (aNext === b) {
          aParent.insertBefore(b, a)
        } else if (bNext === a) {
          aParent.insertBefore(a, b)
        } else {
          aParent.insertBefore(b, aNext)
          aParent.insertBefore(a, bNext)
        }
      } else {
        const placeholder = document.createElement('div')
        aParent.insertBefore(placeholder, a)
        bParent.insertBefore(a, bNext)
        aParent.insertBefore(b, placeholder)
        aParent.removeChild(placeholder)
      }
    } else {
      this.element.appendChild(this.dragged)
    }

    if (this.dragged) {
      const draggedCard = this.dragged.firstElementChild
      if (draggedCard) {
        const prev = draggedCard.dataset._prevStyle || ''
        draggedCard.style.boxShadow = '0 0 0 8px rgba(59,130,246,0.35)'
        setTimeout(() => {
          if (draggedCard) draggedCard.setAttribute('style', prev)
        }, 400)
      }
    }
    this.cleanupAfterDrop()
    this.items = Array.from(this.element.querySelectorAll('[data-id]'))
    this.syncOrder()
  }

  onDragEnd() {
    if (this.dragged) {
      const draggedCard = this.dragged.firstElementChild
      if (draggedCard) {
        draggedCard.setAttribute('style', draggedCard.dataset._prevStyle || '')
        delete draggedCard.dataset._prevStyle
      } else {
        this.dragged.setAttribute('style', this.dragged.dataset._prevStyle || '')
        delete this.dragged.dataset._prevStyle
      }
    }
    this.cleanupAfterDrop()
  }

  cleanupAfterDrop() {
    this.items.forEach(i => {
      const card = i.firstElementChild
      if (card) {
        card.setAttribute('style', card.dataset._prevStyle || '')
        delete card.dataset._prevStyle
      }
      i.classList.remove('ring-4', 'ring-primary/40', 'bg-primary/5')
    })
     this.hoverTarget = null
      this.dragged = null
   }

  async syncOrder() {
    const ids = Array.from(this.element.querySelectorAll('[data-id]')).map(el => el.dataset.id)
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content || ''

    try {
      const res = await fetch(this.urlValue, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json'
        },
        body: JSON.stringify({ order: ids })
      })

      if (!res.ok) {
        console.error('Failed to save order', await res.text())
      }
    } catch (err) {
      console.error('Network error while saving order', err)
    }
  }
}
