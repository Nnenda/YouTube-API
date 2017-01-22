$('document').ready(function(){

	var clickDown = true;
	var firstEnter = true;
	var counterPr = 0;
	var scrollY = document.body.scrollTop;
	var inUp;

    window.onscroll = function(){

        if(document.body.scrollTop == 0)
            inUp = true;

        if(firstEnter){
            if(document.body.scrollTop > 200)
                $('.upper').fadeIn();
            else
                $('.upper').fadeOut();
        }
        else $('.upper').fadeIn();

        if(!clickDown && inUp){
            if(document.body.scrollTop > 400){
                $('.upper').fadeOut();
                setTimeout(function() {$('.upper').attr('src', '..\\images\\ArrowUp.png')}, 400);
                firstEnter = true;
                clickDown = true;
            }

        }

    }

    $('.upper').click(function(){

        if(clickDown){
            scrollY = document.body.scrollTop;
            $('body').animate({scrollTop: 0}, 600);
            $('.upper').fadeOut();
            setTimeout(function() {$('.upper').attr('src', '..\\images\\ArrowDown.png')}, 400);
            clickDown = false;
            firstEnter = false;
            inUp = false;
        }
        else{
            counterPr = 0;
            $('body').animate({scrollTop: scrollY}, 600);
            $('.upper').fadeOut();
            setTimeout(function() {$('.upper').attr('src', '..\\images\\ArrowUp.png')}, 400);
            clickDown = true;
            firstEnter = true;
        }
    })

})
