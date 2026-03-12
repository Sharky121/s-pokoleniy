function Vector(x, y) {
    this.x = x;
    this.y = y;
}

Object.defineProperty(Vector.prototype,'length',{get: function() {
        return Math.sqrt(this.x * this.x + this.y * this.y);
    }});

Vector.prototype.dot = function(v) {
    return this.x * this.y + v.x * v.y;
};

Vector.prototype.rotateAround = function(o, a) {
    let v = this, c = Math.cos(a), s = Math.sin(a), x, y;
    x = (v.x - o.x) * c - (v.y - o.y) * s + o.x;
    y = (v.x - o.x) * s + (v.y - o.y) * c + o.y;
    return new Vector(x,y);
};

const APPLE = /ipad|iphone|ipod/.test(navigator.userAgent.toLowerCase()) && !window.MSStream;

/*--------------------------------------------------------------------------------------------------------------------*/

let SimpleSlider = (function() {
    let container = null, images = [], button = {prev: null, next: null}, current = 0, sourceX = 0;

    function buttonClick(event) {
        current += event.currentTarget.getAttribute('data-ss-action')==='next' ? 1 : -1;
        current = current<0 ? images.length - 1 : current>=images.length ? 0 : current;
        images.forEach(function(image,index) {
            DOMTokenList.prototype[index===current ? 'add' : 'remove'].call(image.classList,'current');
        });
    }

    function keydown(event) {
        switch (event.which) {
            case 37: button.prev.simulateClick(); break;
            case 39: button.next.simulateClick(); break;
        }
    }

    function simulateClick() {
        if (this.fireEvent) this.fireEvent('onclick'); else {
            let event = document.createEvent('Events');
            event.initEvent('click',true,false);
            this.dispatchEvent(event);
        }
    }

    function touchstart(event) {
        sourceX = event.changedTouches[0].pageX;
    }

    function touchend(event) {
        let delta = event.changedTouches[0].pageX - sourceX;
        if (Math.abs(delta)>20) button[delta>0 ? 'next' : 'prev'].simulateClick();
    }

    function initializeSlider() {
        for (let action in button) {
            button[action].addEventListener('click',buttonClick);
            button[action].simulateClick = simulateClick;
        }
        container.addEventListener('touchstart',touchstart);
        container.addEventListener('touchend',touchend);
        window.addEventListener('keydown',keydown);
    }

    return {
        initialize: function() {
            container = document.querySelector('[data-simple-slider]');
            if (container!==null) {
                images = [].slice.call(container.querySelectorAll('[data-ss-image]'));
                button.prev = container.querySelector('[data-ss-action="prev"]');
                button.next = container.querySelector('[data-ss-action="next"]');
                if (button.prev!==null && button.next!==null) initializeSlider();
            }
        }
    };
})();

let YandexMap = (function() {
    const MAP_ID = 'yaMap', COORD = [55.748493, 37.608526], PLACEMARK_SIZE = 48;
    let element = null, map = null, placemark = null;

    function init() {
        map = new ymaps.Map(MAP_ID, {center: COORD, zoom: 17});

        placemark =  new ymaps.Placemark(COORD,{},{
                iconLayout: 'default#image',
                iconImageHref: '/images/icons/location.svg',
                iconImageSize: [PLACEMARK_SIZE, PLACEMARK_SIZE],
                iconImageOffset: [-PLACEMARK_SIZE / 2, -PLACEMARK_SIZE]
            }
        );

        map.behaviors.disable('scrollZoom');

        map.geoObjects.add(placemark);

        if ((function() {
            let
                userAgent = navigator.userAgent.toLowerCase(),
                iOS = /ipad|iphone|ipod/.test(userAgent) && !window.MSStream,
                android = userAgent.indexOf('android')>=0;
            return iOS || android;
        })()) map.behaviors.disable('drag');
    }

    return {
        initialize: function () {
            element = document.getElementById(MAP_ID);
            if (element!==null) ymaps.ready(init);
        }
    };
})();

let Menu = (function() {

    const ATTR_ACTION = 'data-menu-action', ATTR_MENU = 'data-menu';
    let menu = null, body = document.body;

    const actions = {
        open: function() {
            body.setAttribute(ATTR_MENU,'true');
            if (APPLE) {
                document.querySelector('header').setAttribute('style','z-index: 1 !important');
                document.querySelector('.main-content').setAttribute('style','overflow-y:visible !important');
            }
        },
        close: function() {
            body.removeAttribute(ATTR_MENU);
            if (APPLE) {
                document.querySelector('header').removeAttribute('style');
                document.querySelector('.main-content').removeAttribute('style');
            }
        }
    };

    function toggleMenu(event) {
        let action = event.currentTarget.getAttribute(ATTR_ACTION);
        if (actions.hasOwnProperty(action)) actions[action]();
    }

    function setupEvents() {
        [].slice.call(document.querySelectorAll('['+ATTR_ACTION+']')).forEach(function(element) {
            element.addEventListener('click',toggleMenu);
        });
    }

    return {
        initialize: function()  {
            menu = document.getElementsByTagName('aside');
            menu = menu.length>0 ? menu[0] : null;
            if (menu!==null) setupEvents();
        }
    };
})();

let DynamicQuote = (function() {
    let blockquote = null, current = -1;
    const CHANGING_DURATION = 1000, CHANGING_INTERVAL = 9000;

    function lerp(a, b, x) {
        return a + (b - a) * x;
    }

    function switchQuote() {
        let newCurrent = current;
        while (newCurrent === current) {
            newCurrent++;
            if (newCurrent >= QUOTES.length) {
                newCurrent = 0;
                break;
            }
        }
        current = newCurrent;
        return QUOTES[current];
    }

    function nextQuote() {
        let start = performance.now(), fadeIn = false;
        requestAnimationFrame(function step(time) {
            let x = (time - start) / CHANGING_DURATION;
            x = x<0 ? x : x>1 ? 1 : x;
            if (!fadeIn && x>0.5) {
                blockquote.innerHTML = switchQuote().html;
                fadeIn = true;
            }
            blockquote.style.opacity = Math.abs(x * 2 - 1);
            if (x<1) requestAnimationFrame(step); else blockquote.removeAttribute('style');
        });
    }

    function initializeQuotes() {
        QUOTES.forEach(function(quote) {
            Object.defineProperty(quote,'html',{get: function() {return this.quote + '<span class="author">' + this.author + '</span>';}});
        });
        blockquote.innerHTML = switchQuote().html;
    }

    return {
        initialize: function() {
            blockquote = document.querySelector('main.blocks > blockquote');
            if (blockquote!==null) {
                initializeQuotes();
                setInterval(nextQuote,CHANGING_INTERVAL);
            }
        }
    };
})();

let MainPageAnimation = (function() {

    let srcIcons = null;

    function Icon(block) {
        this.div = document.createElement('div');
        this.div.className = 'a';
        // this.div.style.backgroundImage = 'url(\'%s\')'.replace(/%s/g,block.container.getElementsByTagName('img')[0].src);
        this.div.style.backgroundImage = 'url(\'%s\')'.replace(/%s/g,srcIcons[Math.floor(Math.random()*srcIcons.length)].src);
        this.div.style.opacity = 0;
        this.block = block.container;
        this.rect = block.rect;
        this.movement = {x: 1, y: 0};
        block.container.appendChild(this.div);
        this.reinitPosition();
        this.reinitMovement();
    }

    Object.defineProperty(Icon.prototype,'x',{
        get: function() {
            return parseFloat(this.div.style.left);
        },
        set: function (value) {
            this.div.style.left = value.toString() + 'px';
        }
    });

    Object.defineProperty(Icon.prototype,'y',{
        get: function() {
            return parseFloat(this.div.style.top);
        },
        set: function (value) {
            this.div.style.top = value.toString() + 'px';
        }
    });

    Object.defineProperty(Icon.prototype,'outOfSight',{
        get: function() {
            return this.x<-ICON_SIZE || this.x>this.rect.width || this.y<-ICON_SIZE || this.y>this.rect.height;
        }
    });

    Icon.prototype.overlaps = function(icons) {
        let px = parseFloat(this.div.style.left), py = parseFloat(this.div.style.top);
        for (let i=0; i<icons.length; i++) {
            if (icons[i]===this) continue;
            let x = parseFloat(icons[i].div.style.left), y = parseFloat(icons[i].div.style.top);
            x -= px; y -= py;
            if (Math.sqrt(x * x + y * y)<ICON_SIZE) return true;
        }
        return false;
    };

    Icon.prototype.reinitMovement = function () {
        let a = random(-Math.PI,Math.PI);
        this.movement = {x: Math.cos(a), y: Math.sin(a)};
    };

    Icon.prototype.reinitPosition = function () {
        this.div.style.left = random(0,this.rect.width-ICON_SIZE) + 'px';
        this.div.style.top = random(0,this.rect.height-ICON_SIZE) + 'px';
    };

    const ICONS_AMOUNT = 20, ICON_SIZE = 40, MOVEMENT_SPEED = 0.02, APPEAR_SPEED = 0.02, MAX_OPACITY = 0.25;
    let blocks = [];

    function resize() {
        for (let i=0; i<blocks.length; i++) blocks[i].rect = blocks[i].container.getBoundingClientRect();
    }

    function random(min, max) {
        return Math.random() * (max - min) + min;
    }

    function angleBetween(a,b) {
        return Math.acos(a.dot(b) / (a.length * b.length));
    }

    function collide(icon1,icon2) {
        if (icon1===icon2) return;
        const Z = new Vector(0,0);
        let
            a = new Vector(parseFloat(icon1.div.style.left) + ICON_SIZE / 2, parseFloat(icon1.div.style.top) + ICON_SIZE / 2),
            b = new Vector(parseFloat(icon2.div.style.left) + ICON_SIZE / 2, parseFloat(icon2.div.style.top) + ICON_SIZE / 2),
            delta = new Vector(b.x - a.x,b.y - a.y);
        if (delta.length<ICON_SIZE) {
            let
                m1 = new Vector(icon1.movement.x,icon1.movement.y),
                m2 = new Vector(icon2.movement.x,icon2.movement.y),
                angle = angleBetween(m1,m2);
            m1 = m1.rotateAround(Z,Math.PI - angle);
            m2 = m2.rotateAround(Z,Math.PI - angle);
            icon1.movement.x = m1.x;
            icon1.movement.y = m1.y;
            icon2.movement.x = m2.x;
            icon2.movement.y = m2.y;
            let add = ICON_SIZE - delta.length;
            icon1.x += icon1.movement.x * add;
            icon1.y += icon1.movement.y * add;
            icon2.x += icon2.movement.x * add;
            icon2.y += icon2.movement.y * add;
        }
    }

    function initializeBlock(block) {
        for (let i=0; i<ICONS_AMOUNT; i++) {
            let icon = new Icon(block);
            while (icon.overlaps(block.icons)) icon.reinitPosition();
            block.icons.push(icon);
        }
    }

    function runMovement() {
        let prev = performance.now();
        requestAnimationFrame(function step(time) {
            let delta = (time - prev) * MOVEMENT_SPEED;
            prev = time;
            blocks.forEach(function(block) {
                block.icons.forEach(function(icon) {
                    icon.x += icon.movement.x * delta;
                    icon.y += icon.movement.y * delta;
                    let opacity = parseFloat(icon.div.style.opacity);
                    if (opacity<MAX_OPACITY) {
                        opacity += delta * APPEAR_SPEED;
                        if (opacity>MAX_OPACITY) opacity = MAX_OPACITY;
                        icon.div.style.opacity = opacity;
                    }
                    if (icon.outOfSight) {
                        icon.reinitMovement();
                        icon.reinitPosition();
                        while (icon.overlaps(block.icons)) icon.reinitPosition();
                        icon.div.style.opacity = 0;
                    }
                });
                for (let i=0; i<block.icons.length; i++) for (let j=i+1; j<block.icons.length; j++) collide(block.icons[i],block.icons[j]);
            });
            requestAnimationFrame(step);
        });
    }

    return {
        initialize: function() {
            if (Math.max(document.documentElement.clientWidth,window.innerWidth || 0)<640) return;
            blocks = [].slice.call(document.querySelectorAll('main.main > a.vertical-block')).map(function(block) {
                return {container: block, rect: null, icons: []};
            });
            srcIcons = [].slice.call(document.querySelectorAll('main.main > a.vertical-block img'));
            resize();
            blocks.forEach(initializeBlock);
            if (blocks.length>0) {
                runMovement();
                window.addEventListener('resize',resize);
            }
        }
    };
})();

let SameHeightBlocks = (function() {

    let blocks = [];

    function columns() {
        let vw = Math.max(document.documentElement.clientWidth,window.innerWidth || 0);
        return vw<480 ? 1 : vw<600 ? 2 : 3;
    }

    function recalculate() {
        blocks.forEach(function(row) {
            let height = row.map(function(block) {
                return block.getBoundingClientRect().height;
            }).reduce(function(p,c) {
                return c>p ? c : p;
            },0);
            row.forEach(function(block) {
                block.style.height = height + 'px';
            });
        });
    }

    return {
        initialize: function() {
            let colNum = columns();
            if (colNum<2) return;
            let tmp = [].slice.call(document.querySelectorAll('main.blocks > .block.block__third')), row = [];
            tmp.forEach(function(block,index) {
                let col = index % colNum;
                if (col===0) row = [];
                row.push(block);
                if (col===colNum-1 || index===tmp.length-1 && row.length>0) blocks.push(row);
            });
            window.addEventListener('load',function() {
                recalculate();
                window.addEventListener('resize',recalculate);
            });
        }
    };
})();

[
    SimpleSlider,
    YandexMap,
    Menu,
    DynamicQuote,
    MainPageAnimation,
    SameHeightBlocks
].forEach(function(module) {
    module.initialize();
});