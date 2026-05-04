import toastr from 'toastr';

window.toastr = toastr;



window.addEventListener('livewire:navigated', function(){
    const Carousels = document.querySelectorAll('.carousel');

    Carousels.forEach(carousel => {
        const items = carousel.querySelectorAll('.carousel-item');
        let currentIndex = 0;

        items.forEach((item, index) => {
            if(index == currentIndex){
                item.classList.add('block');
            } else {
                item.classList.add('hidden');
            }
        });

        function showItem(index){
            items.forEach((item, i) => {
                if(i == index){
                    item.classList.remove('hidden');
                    item.classList.add('block');
                } else {
                    item.classList.add('hidden');
                    item.classList.remove('block');
                }
            });
            updateDots();
        }

        const leftButton = document.createElement('button');
        leftButton.classList.add('absolute', 'left-3', 'top-1/2', 'transform', '-translate-y-1/2', 'bg-gray-800/50', 'text-white', 'p-2', 'rounded-md', "cursor-pointer");
        leftButton.innerHTML = '&#8592;';
        leftButton.addEventListener('click', () => {
            currentIndex = (currentIndex - 1 + items.length) % items.length;
            showItem(currentIndex);
        });

        const rightButton = document.createElement('button');
        rightButton.classList.add('absolute', 'right-3', 'top-1/2', 'transform', '-translate-y-1/2', 'bg-gray-800/50', 'text-white', 'p-2', 'rounded-md', "cursor-pointer");
        rightButton.innerHTML = '&#8594;';
        rightButton.addEventListener('click', () => {
            currentIndex = (currentIndex + 1) % items.length;
            showItem(currentIndex);
        });

        const indexElement = document.createElement('div');
        indexElement.classList.add('absolute', 'bottom-3', 'left-1/2', 'transform', '-translate-x-1/2', 'bg-gray-800/50', 'text-white', 'px-3', 'py-1', 'rounded-md', "text-sm");
        
        items.forEach((item, index) => {
            var dot = document.createElement('span');
            dot.classList.add('inline-block', 'w-2', 'h-2', 'mx-1', 'rounded-full', 'bg-gray-400', 'cursor-pointer');
            if(index == currentIndex){
                dot.classList.add('bg-white');
            }
            dot.addEventListener('click', () => {
                currentIndex = index;
                showItem(currentIndex);
            });

            indexElement.appendChild(dot);
        });

        function updateDots(){
            const dots = indexElement.querySelectorAll('span');
            dots.forEach((dot, index) => {
                if(index == currentIndex){
                    dot.classList.add('bg-white');
                    dot.classList.remove('bg-gray-400');
                } else {
                    dot.classList.remove('bg-white');
                    dot.classList.add('bg-gray-400');
                }
            });
        }
        
        carousel.appendChild(indexElement);
        carousel.appendChild(leftButton);
        carousel.appendChild(rightButton);

        if(carousel.classList.contains('autoplay')){
            setInterval(() => {
                currentIndex = (currentIndex + 1) % items.length;
                showItem(currentIndex);
            }, 10000);
        }

    });


})
/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import './echo';
