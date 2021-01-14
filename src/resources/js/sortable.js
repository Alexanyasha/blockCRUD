$(document).ready(function(){

    $.fn.draggable = function(){
        // функция отмены выделения текста
        function disableSelection(){
            return false;
        }
        // нажали на элементе
        $(this).mousedown(function(e){
            var drag = $(this);
            // верхняя граница контейнера
            var posParentTop = drag.parent().offset().top;
            // нижняя граница контейнера
            var posParentBottom = posParentTop + drag.parent().height();
            // координаты исходного положения элемента
            var posOld = drag.offset().top;
            // коррекция относительно позиции курсора при нажатии
            var posOldCorrection = e.pageY - posOld;
            // поднимаем нажатый элемент по z-оси
            drag.css({'z-index':2, 'background-color':'#eeeeee'});
            // перетягиваем элемент
            var mouseMove = function(e){
                // получаем новые динамические координаты элемента
                var posNew = e.pageY - posOldCorrection;
                // если элемент перетянут выше верхней границы контейнера
                if (posNew < posParentTop){
                    // устанавливаем позицию элемента, равную позиции родителя
                    drag.offset({'top': posParentTop});
                    // меняем элемент с предыдущим в DOM, если он (предыдущий элемент) существует
                    // замещаемый элемент перемещаем плавно, с анимацией
                    if (drag.prev().length > 0 ) {
                        drag.insertBefore(drag.prev().css({'top':-drag.height()}).animate({'top':0}, 100));
                    }
                // если элемент перетянут ниже нижней границы контейнера
                } else if ((posNew + drag.height()) > posParentBottom){
                    // устанавливаем позицию элемента, равную позиции родителя + высоте родителя - высоте элемента
                    drag.offset({'top': posParentBottom - drag.height()});
                    // меняем элемент со следующим в DOM, если он (следующий элемент) существует
                    // замещаемый элемент перемещаем плавно, с анимацией
                    if (drag.next().length > 0 ) {
                        drag.insertAfter(drag.next().css({'top':drag.height()}).animate({'top':0}, 100));
                    }
                // если элемент в пределах контейнера
                } else {
                    // устанавливаем новую высоту (элемент перемещается за курсором)
                    drag.offset({'top': posNew});
                    // если элемент перемещен вверх на собственную высоту
                    if (posOld - posNew > drag.height() - 1){
                        // меняем элемент с предыдущим в DOM
                        drag.insertBefore(drag.prev().css({'top':-drag.height()}).animate({'top':0}, 100));
                        // обнуляем позицию
                        drag.css({'top':0});
                        // снова получаем координаты исходного и текущего положения
                        posOld = drag.offset().top;
                        posNew = e.pageY - posOldCorrection;
                        posOldCorrection = e.pageY - posOld;
                    // если элемент перемещен вниз на собственную высоту
                    } else if (posNew - posOld > drag.height() - 1){
                        // меняем элемент со следующим в DOM
                        drag.insertAfter(drag.next().css({'top':drag.height()}).animate({'top':0}, 100));
                        drag.css({'top':0});
                        posOld = drag.offset().top;
                        posNew = e.pageY - posOldCorrection;
                        posOldCorrection = e.pageY - posOld;
                    }
                }
            };

            var dragParent = drag.parent();
            
            // отпускаем клавишу мыши
            var mouseUp = function(){
                // завершаем выполнение функции
                $(document).off('mousemove', mouseMove).off('mouseup', mouseUp);
                // отключаем функцию отмены выделения текста
                $(document).off('mousedown', disableSelection);
                // плавно возвращаем наш элемент на ближайшее освободившееся место
                drag.animate({'top':0}, 100, function(){
                    // возвращаем z-позицию на уровень остальных элементов
                    drag.css({'z-index':1, 'background-color':'transparent'});
                });

                // активация события
                dragParent.trigger('changeSortable', [drag]);
            };
            // подключаем выполнение функций перемещения и отпускания клавиши мыши
            // завершаем выполнение функции, если нажата правая клавиша мыши
            $(document).on('mousemove', mouseMove).on('mouseup', mouseUp).on('contextmenu', mouseUp);
            // включаем функцию отмены выделения текста
            $(document).on('mousedown', disableSelection);
            // завершаем выполнение, если окно потеряло фокус (например, переключение на другую вкладку)
            $(window).on('blur', mouseUp);
        });
    }
    
    $('.drag').draggable();
    
});
