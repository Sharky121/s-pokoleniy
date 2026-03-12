let Gallery = (function() {
    const ATTR_GALLERY = 'data-gallery', ATTR_INDEX = 'data-index';
    let galleries = {}, image = null, previews = null, buttons = null, img = null, numbers = null;

    function div(className) {
        let element = document.createElement('div');
        element.className = className;
        return element;
    }

    function svg(path) {
        let
            elementSVG = document.createElementNS('http://www.w3.org/2000/svg','svg'),
            elementPath = document.createElementNS('http://www.w3.org/2000/svg','path');
        elementSVG.setAttribute('width',24);
        elementSVG.setAttribute('height',24);
        elementSVG.setAttribute('viewBox',[0,0,24,24].join(' '));
        elementPath.setAttribute('d',path);
        elementSVG.appendChild(elementPath);
        return elementSVG;
    }

    function switchImage(event) {
        let
            next = event.currentTarget.classList.contains('next'),
            index = image.getAttribute('data-current') * 1,
            gallery = image.getAttribute('data-gallery-name');
        if (!(next || event.currentTarget.classList.contains('prev'))) {
            index = event.currentTarget.getAttribute(ATTR_INDEX) * 1;
            (function(current) {
                if (current!==null) current.classList.remove('current');
            })(document.querySelector('.gallery__previews > img.current'));
        } else {
            previews.childNodes[index].classList.remove('current');
            if (next) index++; else index--;
            if (index>=galleries[gallery].length) index = 0;
            if (index<0) index = galleries[gallery].length - 1;
        }
        previews.childNodes[index].classList.add('current');
        image.setAttribute('data-current',index);
        setNumbers(index,null);
        img.setAttribute('src',galleries[gallery][index].url);
    }

    function initializeDOMElements() {
        const CSS_640PX = [
            '.gallery__previews{display:none}',
            '.gallery__image{height:calc(100% - 50px)}',
            '.specialist-details .vtab .images-tile{overflow-x:scroll}',
            '.gallery__number{display:block}'
        ];
        const CSS = [
            '.gallery__backdrop{background-color:rgba(0,0,0,0.75);display:block;height:100vh;left:0;opacity:0;pointer-events:none;position:fixed;top:0;transition:opacity 0.15s ease-in-out;-webkit-user-select:none;-moz-user-select:none;user-select:none;width:100vw;z-index:100}',
            'body['+ATTR_GALLERY+']{overflow:hidden}',
            'body['+ATTR_GALLERY+'] .gallery__backdrop{opacity:1;pointer-events:initial}',
            // '.gallery__image {background:transparent none no-repeat scroll center;background-size:contain;height:calc(100vh - 150px);left:0;position:absolute;top:25px;transition:all 0.15s ease-in-out;width:100%}',
            '.gallery__image {align-items:center;display:flex;flex-direction:column;flex-wrap:nowrap;height:calc(100vh - 150px);justify-content:center;left:0;position:absolute;top:25px;transition:all 0.15s ease-in-out;width:100%}',
            '.gallery__image img{max-height:100%;max-width:100%}',
            '.gallery__button{background-color:rgba(0,0,0,0.5);border-radius:100%;cursor:pointer;height:5vmin;min-height:37px !important;min-width:37px !important;opacity:0.75;position:absolute;top:calc(50vh - 2.5vmin);transition:opacity 0.15s ease-in-out;width:5vmin}',
            '.gallery__button:hover{opacity:1}',
            '.gallery__button.prev{left:2vmin}',
            '.gallery__button.next{right:2vmin}',
            '.gallery__button svg{height:3vmin;left:1vmin;min-height:16.2px !important;min-width:16.2px !important;position:absolute;top:1vmin;width:3vmin}',
            '.gallery__button.prev svg{transform:scaleX(-1)}',
            '.gallery__button svg path{fill:white}',
            '.gallery__previews{align-items:center;border-top:solid 1px rgba(255,255,255,0.25);bottom:0;display:flex;flex-direction:row;flex-wrap:nowrap;height:100px;justify-content:center;left:10px;position:absolute;width:calc(100vw - 20px)}',
            '.gallery__previews > img{cursor:pointer;height:80px;margin:0 5px;transition:all 0.15s ease-in-out;width:auto}',
            '.gallery__previews > img:hover,.gallery__previews > img.current{transform:scale(1.2)}',
            '.gallery__close{cursor:pointer;height:24px;opacity:0.75;position:absolute;right:24px;top:24px;transition:all 0.15s ease-in-out;width:24px}',
            '.gallery__close:hover{opacity:1;transform:rotate(90deg)}',
            '.gallery__close svg{height:24px;width:24px}',
            '.gallery__close svg path{fill:white}',
            '.gallery__number{background-color:rgba(0,0,0,0.75);border-radius:16px;bottom:32px;color:white;display:none;font-size:19.2px;height:32px;left:calc(50vw - 35px);line-height:32px;opacity:0.75;pointer-events:none;position:absolute;text-align:center;width:70px}',
            '@media screen and (max-width:639px){'+CSS_640PX.join('')+'}',
            '@media screen and (max-width:539px){.gallery__button svg{left:10.4px;top:10.4px}}'
        ];
        let
            backdrop = div('gallery__backdrop'),
            style = document.createElement('style'),
            closer = div('gallery__close');
        numbers = div('gallery__number');
        img = document.createElement('img');
        buttons = {prev: div('gallery__button prev'), next: div('gallery__button next')};
        image = div('gallery__image');
        previews = div('gallery__previews');
        img.setAttribute('alt','');
        style.setAttribute('type','text/css');
        style.innerHTML = CSS.join('');
        for (let button in buttons) {
            buttons[button].appendChild(svg('M5 3l3.057-3 11.943 12-11.943 12-3.057-3 9-9z'));
            buttons[button].addEventListener('click',switchImage);
        }
        closer.appendChild(svg('M23.954 21.03l-9.184-9.095 9.092-9.174-2.832-2.807-9.09 9.179-9.176-9.088-2.81 2.81 9.186 9.105-9.095 9.184 2.81 2.81 9.112-9.192 9.18 9.1z'));
        closer.addEventListener('click',hideGallery);
        image.appendChild(img);
        numbers.innerHTML = '0/0';
        [image,buttons.prev,buttons.next,previews,closer,numbers].forEach(function(child) {
            backdrop.appendChild(child);
        });
        document.body.insertBefore(style,document.body.firstChild);
        document.body.appendChild(backdrop);
    }

    function createImageRow(row) {
        let
            gallery = row.getAttribute('data-gallery-id'),
            container = document.createElement('div'),
            scroller = document.createElement('div'),
            buttons = [document.createElement('button'),document.createElement('button')],
            images = [document.createElement('img'),document.createElement('img')];
        row.className = 'image-row';
        container.className = 'image-row__container';
        scroller.className = 'image-row__scroller';
        buttons[0].className = 'disabled';
        images[0].src = '/images/icons/arrow-down.svg';
        images[0].alt = '';
        images[1].src = '/images/icons/arrow-down.svg';
        images[1].alt = '';
        buttons[0].appendChild(images[0]);
        buttons[1].appendChild(images[1]);
        container.appendChild(scroller);
        galleries[gallery].forEach(function(g,i) {
            let
                div = document.createElement('div'),
                item = document.createElement('div'),
                image = document.createElement('img');
            item.className = 'image-row__item';
            item.setAttribute('data-gallery',gallery);
            item.setAttribute('data-index',i);
            image.src = g.preview;
            image.alt = '';
            item.appendChild(image);
            div.appendChild(item);
            scroller.appendChild(div);
        });
        row.appendChild(container);
        row.appendChild(buttons[0]);
        row.appendChild(buttons[1]);
    }

    function setNumbers(current, total) {
        if (current===null || total===null) {
            let s = numbers.innerHTML.split('/').map(function(x) {return x*1;});
            current = current===null ? s[0] : current;
            total = total===null ? s[1] : total;
        }
        numbers.innerHTML = [current+1,total].join('/');
    }

    function showGallery(name,index) {
        if (!galleries.hasOwnProperty(name)) return;

        galleries[name].forEach(function(image,index) {
            let preview = document.createElement('img');
            preview.setAttribute('src',image.preview);
            preview.setAttribute('alt','');
            preview.setAttribute(ATTR_INDEX,index);
            preview.addEventListener('click',switchImage);
            previews.appendChild(preview);
        });
        previews.childNodes[index].classList.add('current');
        img.setAttribute('src',galleries[name][index].url);
        image.setAttribute('data-current',index);
        image.setAttribute('data-gallery-name',name);
        setNumbers(index,galleries[name].length);
        document.body.setAttribute(ATTR_GALLERY,name);
    }

    function hideGallery() {
        document.body.removeAttribute(ATTR_GALLERY);
        while (previews.firstChild !== null) previews.removeChild(previews.firstChild);
    }

    function elementClick(event) {
        let
            element = event.currentTarget,
            gallery = element.getAttribute(ATTR_GALLERY),
            current = element.hasAttribute(ATTR_INDEX) ? element.getAttribute(ATTR_INDEX) * 1 : 0;
        showGallery(gallery,current);
        event.preventDefault();
    }

    function setupKeyboardEvents() {
        window.addEventListener('keyup',function(event) {
            if (!document.body.hasAttribute(ATTR_GALLERY)) return;
            switch (event.which) {
                case 37:
                    buttons.prev.click();
                    break;
                case 39:
                    buttons.next.click();
                    break;
            }
        });
    }

    return {
        initialize: function () {
            let vars = [].slice.call(document.querySelectorAll('script[data-gallery-id]'));
            vars.forEach(function(_var) {
                let id = _var.getAttribute('data-gallery-id'), json = _var.innerHTML.trim();
                galleries[id] = JSON.parse(json).map(function(item) {
                    if (!item.hasOwnProperty('preview')) item.preview = item.url;
                    return item;
                });
            });
            initializeDOMElements();
            [].slice.call(document.querySelectorAll('div[data-gallery-id]')).forEach(createImageRow);
            let elements = [].slice.call(document.querySelectorAll(':not(body)['+ATTR_GALLERY+']'));
            elements.forEach(function(element) {
                element.addEventListener('click',elementClick);
            });
            setupKeyboardEvents();
        }
    };
})();