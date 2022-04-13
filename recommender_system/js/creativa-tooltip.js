class CreativaTooltip {

    static version = '0.1';
    static cdn = 'https://creativajs.altervista.org/tooltip/0.1/';

    static settings = {
        animationSpeed: 100,
        openAnimationDelay: 150,
        borderRadius: '5px',
        backgroundColor: '#000',
        color: '#fff',
        fontSize: '12px',
        fontWeight: 500,
        lineHeight: '16px'
    }

}

window.onload = function() {

    // CSS load
    const head  = document.getElementsByTagName('head')[0];
    const link  = document.createElement('link');
    link.rel  = 'stylesheet';
    link.type = 'text/css';
    link.href = CreativaTooltip.cdn + 'creativa-tooltip.css';
    link.media = 'all';
    head.appendChild(link);

    // Get all tooltips
    let tooltips = document.querySelectorAll('*[ct-tooltip]');

    for (const tooltip of tooltips) {

        // Get tooltip text and position
        const tooltipText = tooltip.getAttribute('ct-tooltip');
        let tooltipPosition = tooltip.getAttribute('ct-tooltip-postition');
        if (!tooltipPosition) tooltipPosition = 'top';

        // Create tooltip element
        const tooltipElement = document.createElement('span');

        // Settings
        const settings = CreativaTooltip.settings;
        tooltipElement.style.display = 'none';
        tooltipElement.style.backgroundColor = settings.backgroundColor + '99';
        tooltipElement.style.borderRadius = settings.borderRadius;
        tooltipElement.style.animationDuration = settings.animationSpeed / 1000 + 's';
        tooltipElement.style.fontSize = settings.fontSize;
        tooltipElement.style.fontWeight = settings.fontWeight;
        tooltipElement.style.lineHeight = settings.lineHeight;
        tooltipElement.style.color = settings.color;

        // Tooltip classes and append to DOM
        tooltipElement.classList.add('ct-tooltiptext');
        tooltipElement.classList.add('ct-tooltiptext-' + tooltipPosition);
        tooltipElement.innerHTML = tooltipText;
        tooltip.classList.add('ct-tooltip');
        tooltip.appendChild(tooltipElement);

        // Disable context menu on mobile devices
        tooltip.oncontextmenu = function(event) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();
            return false;
        };

        // Show event (mouse hover)
        tooltip.addEventListener('mouseenter', function(e) {
            setTimeout(function() {
                tooltipElement.style.display = 'block';
                tooltipElement.classList.remove('ct-close-animation');
                tooltipElement.classList.add('ct-open-animation');
            }, settings.openAnimationDelay);
        });

        // Hide event (mouse leave)
        tooltip.addEventListener('mouseleave', function() {
            tooltipElement.classList.add('ct-close-animation');
            setTimeout(function() {
                tooltipElement.classList.remove('ct-open-animation');
                tooltipElement.style.display = 'none';
            }, settings.animationSpeed);
        });

    }

}