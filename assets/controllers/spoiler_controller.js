import { Controller } from '@hotwired/stimulus';

/*
 * This stimulus controller activate the spoiler behavior.
 *
 * Any element with a data-controller="spoiler" attribute will cause
 * this controller to be executed.
 */
export default class extends Controller {
    
    static targets = [ "action", "content" ];

    toggle() {
        if(this.actionTarget.innerHTML === 'montrer') {
            this.actionTarget.innerHTML = 'cacher';
        } else {
            this.actionTarget.innerHTML = 'montrer';
        }
        this.contentTarget.classList.toggle('hide');
    }
}
