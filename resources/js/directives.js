import { nextTick } from 'vue';

const select2 = {

    mounted(el, binding) {

        const $el = window.$(el);

        if (!$el.select2) {
            console.warn('Select2 is not available');
            return;
        }


        const $modal = window.$(el).closest('.modal');

        const dropdownParent = $modal.length
            ? $modal
            : window.$('body');


        const options = {
            width: '100%',
            allowClear: true,

            placeholder:
                el.dataset.placeholder || '-- Select --',

            dropdownParent,

            ...(binding.value || {}),
        };

        const getTheme = () => {

            const body = document.body;
            const html = document.documentElement;

            if (
                body.classList.contains('bg-default') ||
                body.getAttribute('data-theme') === 'light' ||
                body.getAttribute('data-bs-theme') === 'light' ||
                html.getAttribute('data-theme') === 'light' ||
                html.getAttribute('data-bs-theme') === 'light'
            ) {
                return 'light';
            }

            if (
                body.classList.contains('dark') ||
                body.classList.contains('dark-theme') ||
                body.classList.contains('theme-dark') ||
                body.classList.contains('dark-mode') ||
                body.getAttribute('data-theme') === 'dark' ||
                body.getAttribute('data-bs-theme') === 'dark' ||
                html.getAttribute('data-theme') === 'dark' ||
                html.getAttribute('data-bs-theme') === 'dark'
            ) {
                return 'dark';
            }


            return 'dark';
        };


        const applyTheme = () => {

            if (!el.isConnected) {
                return;
            }


            const select2Instance = $el.data('select2');

            if (!select2Instance) {
                return;
            }


            const $container =
                select2Instance.$container;

            const $dropdown =
                select2Instance.$dropdown;


            const theme = getTheme();


            $container.removeClass(
                'select2-theme-light select2-theme-dark'
            );

            $dropdown.removeClass(
                'select2-theme-light select2-theme-dark'
            );

            if (theme === 'dark') {

                $container.addClass(
                    'select2-theme-dark'
                );

                $dropdown.addClass(
                    'select2-theme-dark'
                );

            } else {

                $container.addClass(
                    'select2-theme-light'
                );

                $dropdown.addClass(
                    'select2-theme-light'
                );
            }
        };

        $el.select2(options);


        applyTheme();


        $el.on(
            'select2:select select2:unselect',
            () => {

                const event =
                    new Event('change', {
                        bubbles: true
                    });

                el.dispatchEvent(event);
            }
        );



        $el.on('select2:open', () => {

            setTimeout(() => {
                applyTheme();
            }, 0);

        });


        nextTick(() => {

            setTimeout(() => {

                if (!el.isConnected) {
                    return;
                }

                const currentVal = el.value;


                if (
                    currentVal &&
                    $el.find(
                        `option[value="${CSS.escape(currentVal)}"]`
                    ).length
                ) {

                    $el
                        .val(currentVal)
                        .trigger('change.select2');

                } else {

                    $el
                        .val(null)
                        .trigger('change.select2');
                }


                applyTheme();

            }, 300);

        });

        const observer =
            new MutationObserver(() => {

                if (!el.isConnected) {
                    return;
                }


                const currentVal = el.value;


                if ($el.data('select2')) {
                    $el.select2('destroy');
                }


                $el.select2(options);


                if (
                    currentVal &&
                    $el.find(
                        `option[value="${CSS.escape(currentVal)}"]`
                    ).length
                ) {

                    $el
                        .val(currentVal)
                        .trigger('change.select2');

                } else {

                    $el
                        .val(null)
                        .trigger('change.select2');
                }


                applyTheme();

            });


        observer.observe(el, {
            childList: true,
            subtree: true
        });


        el._select2_observer = observer;



        const themeObserver =
            new MutationObserver(() => {

                if (!el.isConnected) {
                    return;
                }


                setTimeout(() => {


                    applyTheme();

                }, 20);

            });


        themeObserver.observe(document.body, {
            attributes: true,

            attributeFilter: [
                'class',
                'data-theme',
                'data-bs-theme'
            ]
        });


        themeObserver.observe(document.documentElement, {
            attributes: true,

            attributeFilter: [
                'class',
                'data-theme',
                'data-bs-theme'
            ]
        });


        el._select2_theme_observer =
            themeObserver;

        el._select2_applyTheme =
            applyTheme;
    },

    updated(el) {

        const $el = window.$(el);

        nextTick(() => {

            if (!el.isConnected) {
                return;
            }


            const currentVal = el.value;


            if (
                currentVal &&
                $el.find(
                    `option[value="${CSS.escape(currentVal)}"]`
                ).length
            ) {

                $el
                    .val(currentVal)
                    .trigger('change.select2');

            } else {

                $el
                    .val(null)
                    .trigger('change.select2');
            }


            if (el._select2_applyTheme) {

                el._select2_applyTheme();

            }

        });
    },

    unmounted(el) {

        if (!el) {
            return;
        }


        try {

            if (window.$) {

                const $el =
                    window.$(el);

                if ($el.data('select2')) {

                    $el.select2('destroy');

                }
            }

            if (el._select2_observer) {

                el._select2_observer.disconnect();

                delete el._select2_observer;
            }

            if (el._select2_theme_observer) {

                el._select2_theme_observer.disconnect();

                delete el._select2_theme_observer;
            }


            delete el._select2_applyTheme;


        } catch (e) {

            console.warn(
                'Select2 cleanup error:',
                e
            );

        }
    }
};


export default (app) => {

    app.directive(
        'select2',
        select2
    );

};