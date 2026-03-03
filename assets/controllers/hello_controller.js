import { Controller } from "@hotwired/stimulus"

export default class extends Controller {
    static targets = ["output", "content"]
    static classes = ["hidden"] // Récupère "d-none" depuis le HTML

    showAdmin() {
        this.reveal()
    }

    showUser() {
        this.hidde()
    }

    reveal(role) {
        // 1. On change le texte
        this.outputTarget.textContent = role

        // 2. On rend la div visible en retirant la classe "hidden"
        this.contentTarget.classList.remove(this.hiddenClass)

    }

    hidde() {
        this.contentTarget.classList.add(this.hiddenClass)
    }

}
