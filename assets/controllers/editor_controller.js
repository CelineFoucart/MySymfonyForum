import { Controller } from '@hotwired/stimulus';

/*
 * This stimulus controller insert bbcode tag in an editor.
 *
 * Any element with a data-controller="editor" attribute will cause
 * this controller to be executed.
 * 
 * To define a button, insert: data-editor-left-param="left-tag" data-editor-right-param="right tag" 
 * data-action="editor#insert"
 */
export default class extends Controller {
    
    static targets = [ "content" ];

    insert(event) {
        event.preventDefault();
        const leftTag = event.params.left;
        const rightTag = event.params.right;
        let message = this.contentTarget;
        let startPos = message.selectionStart;
        let endPos = message.selectionEnd;

        if (document.selection) {
            let string = document.selection.createRange().text;
            message.focus();
            selectedText = document.selection.createRange();
            selectedText.text = leftTag + string + rightTag;
        } else {
            let selectedText = message.value;
            let string = selectedText.substring(message.selectionStart, message.selectionEnd);
            message.value = selectedText.substring(0, startPos) + leftTag + string + rightTag + selectedText.substring(endPos, selectedText.length);
        }
        newPos = startPos + leftTag.length;
        message.setSelectionRange(newPos, newPos);
        message.focus();
    }
}
