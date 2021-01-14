$(document).ready(function() {
    var codeBlocks = document.querySelectorAll('.blockcrud_code_editor');

    for (let i = 0; i < codeBlocks.length; i++) {
        var codeBlock = codeBlocks[i];
        
        if(codeBlock.querySelector('.blockcrud_code_source textarea')) {
            codeBlock.querySelector('.blockcrud_code_source textarea').addEventListener('keyup', refreshPreview);
            codeBlock.querySelector('.blockcrud_code_source textarea').addEventListener('mouseup', refreshPreview);
            $(codeBlock.querySelector('.blockcrud_code_source textarea')).on('refreshPage', refreshPage);
        }
    }

    function refreshPreview(e) {
        var codeBlock = e.target.closest('.blockcrud_code_editor');

        var previewCode = codeBlock.querySelector('.blockcrud_code_source textarea').value,
            shadowBlock = codeBlock.querySelector('.blockcrud_code_preview .blockcrud_preview_area').shadowRoot,
            oldWrapper = shadowBlock.querySelector('.shadow_wrapper');

        shadowBlock.removeChild(oldWrapper);

        var template = document.createElement('div');
        template.setAttribute('class', 'shadow_wrapper');
        template.innerHTML = previewCode;

        shadowBlock.appendChild(document.importNode(template, true));
    }

    function refreshPage(e, blocks) {
        var sort = [],
            codeBlock = e.target.closest('.blockcrud_code_editor'),
            shadowBlock = codeBlock.querySelector('.blockcrud_code_preview .blockcrud_preview_area').shadowRoot,
            oldWrapper = shadowBlock.querySelector('.shadow_wrapper'),
            shadowEls = $(oldWrapper).find('.blockcrud_element');
        
        blocks.each(function() {
            let blockName = $(this).find('.blockcrud_block_slug').text().split('\'')[1];
            sort.push(blockName);
        });

        shadowEls = shadowEls.filter(function() {
            return sort.indexOf($(this).attr('data-block')) !== -1;
        });
        shadowEls.sort(function (a, b) {
            return sort.indexOf($(a).attr('data-block')) - sort.indexOf($(b).attr('data-block'));
        });

        $(oldWrapper).html(shadowEls);
    }

    var toggleBlocks = document.querySelectorAll('.blockcrud_toggle_wrapper');

    for (let j = 0; j < toggleBlocks.length; j++) {
        var toggleBlock = toggleBlocks[j],
            conditions = toggleBlock.querySelectorAll('.blockcrud_toggle_when');

        if(conditions.length) {
            toggleBlock.classList.add('blockcrud_hidden');

            for (let c = 0; c < conditions.length; c++) {
                let input = document.querySelector('[name=' + conditions[c].getAttribute('name').replace('cond_', '') + ']');

                if(input && ! input.getAttribute('data-listen')) {
                    input.addEventListener('change', toggleInput);
                    input.addEventListener('select', toggleInput);
                    input.setAttribute('data-listen', true);

                    setTimeout(function() {
                        var evt = new Event('change');
                        input.dispatchEvent(evt);
                    }, 500);
                }
            }
        }
    }

    function toggleInput(e) {
        var input = e.target.closest('input');

        if(! input) {
            input = e.target.closest('select');
        }

        if(! input) {
            input = e.target.closest('textarea');
        }

        if(input) {
            var wrappers = document.querySelectorAll('.blockcrud_toggle_wrapper');

            for (let w = 0; w < wrappers.length; w++) {
                if(wrappers[w].querySelector('[name=cond_' + input.getAttribute('name') + ']')) {
                    wrappers[w].classList.add('blockcrud_hidden');
                }
            }

            var conditions = document.querySelectorAll('[name=cond_' + input.getAttribute('name') + ']');

            for (let i = 0; i < conditions.length; i++) {
                var wrapper = conditions[i].closest('.blockcrud_toggle_wrapper');

                if(input.value == conditions[i].value) {
                    wrapper.classList.remove('blockcrud_hidden');
                }
            }
        }
    }

    var editorBlocks = document.querySelectorAll('.blockcrud-editable');

    for (let i = 0; i < editorBlocks.length; i++) {
        var editorBlock = editorBlocks[i];
        
        editorBlock.addEventListener('click', enableEditor);
    }

    var codePreviews = document.querySelectorAll('.blockcrud-code-preview .blockcrud_preview_area');

    for (let i = 0; i < codePreviews.length; i++) {
        if(codePreviews[i].shadowRoot) {
            var anchors = codePreviews[i].shadowRoot.querySelectorAll('a');
            for (let a = 0; a < anchors.length; a++) {
                anchors[a].setAttribute('title', anchors[a].getAttribute('href'));
                anchors[a].removeAttribute('href');
            }

            var outerForm = codePreviews[i].closest('form');
            var editorBlocksShadow = codePreviews[i].shadowRoot.querySelectorAll('.blockcrud-editable');

            for (let j = 0; j < editorBlocksShadow.length; j++) {
                var editorBlockShadow = editorBlocksShadow[j];
                
                editorBlockShadow.addEventListener('input', function(e) {
                    enableEditor(e, outerForm);
                }, false);
                editorBlockShadow.addEventListener('change', function(e) {
                    enableEditor(e, outerForm);
                }, false);
                editorBlockShadow.addEventListener('keyup', function(e) {
                    enableEditor(e, outerForm);
                }, false);
                editorBlockShadow.addEventListener('blur', function(e) {
                    enableEditor(e, outerForm);
                }, false);
            }
        }
    }

    function enableEditor(e, outerForm) {
        var field = e.target.closest('.blockcrud-editable'),
            arrName = field.closest('.shadow_wrapper').getAttribute('name'),
            input = outerForm.querySelector('#field_' + field.id);

        if(! input) {
            input = document.createElement('textarea');
            input.id = 'field_' + field.id;
            input.setAttribute('name', arrName + '[' + field.id + ']');
            input.style.display = 'none';
            outerForm.appendChild(input);
        }

        input.value = field.innerHTML;
    }

    var sortableBlocks = document.querySelectorAll('.blockcrud-js-sortable-blocks');

    for (let i = 0; i < sortableBlocks.length; i++) {
        var sortableBlock = sortableBlocks[i];
        
        if(sortableBlock.querySelector('.blockcrud_sortable')) {
            $(sortableBlock.querySelector('.blockcrud_sortable')).on('changeSortable', saveSorting);
        }
    }

    function saveSorting() {
        var liTexts = '',
            th = $(this),
            textarea = th.closest('.blockcrud-js-sortable-blocks').find('.blockcrud-js-sortable-content');

        if(textarea.length) {
            th.find('li').each(function() {
                liTexts += '@' + $(this).find('.blockcrud_block_slug').text() + "\n";
            });

            textarea.val(liTexts).html(liTexts);
            textarea.trigger('refreshPage', [th.find('li')]);
        }
    }

    $(document).on('click', '.js-blockcrud-remove-item', function() {
        var sortable = $(this).closest('.blockcrud_sortable');
        $(this).parent().remove();

        if(sortable[0]) {
            $(sortable[0]).trigger('changeSortable');
        }
    });

    $(document).on('click', '.blockcrud-js-add-block-btn', function() {
        var wrapper = $(this).closest('.blockcrud_code_editor'),
            select = wrapper.find('.blockcrud-js-add-block-name'),
            li = $('<li></li>'),
            html = '';
            
        html = select.find('option:selected').text() + `
            <div class="blockcrud_hidden blockcrud_block_slug">customblock('` + select.val() + `')</div>
            <div class="blockcrud-delete-icon js-blockcrud-remove-item la la-trash" title="Убрать блок со страницы"></div>`;

        li.addClass('drag').addClass('form-control').html(html);
        wrapper.find('.blockcrud_sortable').append(li);
        li.draggable();
        wrapper.find('.blockcrud_sortable').trigger('changeSortable', [li]);
    });
});
