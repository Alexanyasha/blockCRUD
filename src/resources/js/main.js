var codeBlocks = document.querySelectorAll('.blockcrud_code_editor');

for (i = 0; i < codeBlocks.length; i++) {
    var codeBlock = codeBlocks[i];
    
    codeBlock.querySelector('.blockcrud_code_source textarea').addEventListener('keyup', refreshPreview);
}

function refreshPreview(e) {
    var codeBlock = e.target.closest('.blockcrud_code_editor');

    var previewCode = codeBlock.querySelector('.blockcrud_code_source textarea').value,
        shadowBlock = codeBlock.querySelector('.blockcrud_code_preview preview-code').shadowRoot,
        oldWrapper = shadowBlock.querySelector('.shadow_wrapper');

    shadowBlock.removeChild(oldWrapper);

    var template = document.createElement('div');
    template.setAttribute('class', 'shadow_wrapper');
    template.innerHTML = previewCode;

    shadowBlock.appendChild(document.importNode(template, true));
}

