import { Controller } from '@hotwired/stimulus'

export default class extends Controller {
    static targets = ['type', 'countryField', 'nutriscoreField']

    connect() {
        this.updateFields()
    }

    change() {
        this.updateFields()
    }

    updateFields() {
        const type = this.typeTarget.value
        console.log(type);
        console.log(this.countryFieldTarget, this.nutriscoreFieldTarget);

        if (type === 'product_count_by_countries') {
            this.countryFieldTarget.classList.remove('hidden')
            this.nutriscoreFieldTarget.classList.add('hidden')
        } else if (type === 'product_count_by_nutriscore') {
            this.countryFieldTarget.classList.add('hidden')
            this.nutriscoreFieldTarget.classList.remove('hidden')
        } else {
            this.countryFieldTarget.classList.add('hidden')
            this.nutriscoreFieldTarget.classList.add('hidden')
        }
    }
}

