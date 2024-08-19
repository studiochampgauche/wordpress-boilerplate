import React, { useEffect, useState, useRef } from 'react'
import { useNavigate, useLocation } from 'react-router-dom';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

const Transitor = ({ children }) => {
	


	const to = useRef(null);

	const anchorRef = useRef(null);
	const behaviorRef = useRef(null);
	const navigateRef = useRef(useNavigate());

	const { pathname } = useLocation();


	const [isLeaving, setIsLeaving] = useState(false);
	const [isEntering, setIsEntering] = useState(false);


	useEffect(() => {

		ScrollTrigger.refresh();


        /*
        * If SmoothScroller Plugin of GSAP not there,
        * make sure you start on position 0 after a page change
        *
        * If Plugin there, it'll be managed after the leaving transition
        */
        if(!window.gscroll && !anchorRef.current)
        	window.scrollTo(0, 0);

        else if(anchorRef.current)
        	window.gscroll ? window.gscroll.scrollTo(document.getElementById(anchorRef.current), (behaviorRef.current === 'instant' ? false : true), 'top top') : document.getElementById(anchorRef.current).scrollIntoView({behavior: behaviorRef.current});


        /*
        * Prevent default behavior, create your own behavior
        */
        const elements = document.querySelectorAll('a');
        if(!elements.length) return;

        const events = [];

        elements.forEach(item => {

        	const handleClick = (e) => {

        		e.preventDefault();

        		if(!item.hasAttribute('href')) return;


        		const href = item.getAttribute('href');

        		let path = null,
        			anchor = null;

        		try{

        			const url = new URL(href);

        			path = url.pathname;

        			if(url.hash)
        				anchor = url.hash;

        		} catch(_){


        			if(href.includes('#'))
        				[path, anchor] = href.split('#');
        			else
        				path = href;

        		}


        		if(path === pathname && anchor)
        			window.gscroll ? window.gscroll.scrollTo(document.getElementById(anchor), (item.hasAttribute('data-behavior') && item.getAttribute('data-behavior') === 'instant' ? false : true), 'top top') : document.getElementById(anchor).scrollIntoView({behavior: (item.hasAttribute('data-behavior') ? item.getAttribute('data-behavior') : 'auto')});
        		else if(path !== pathname)
        			item.hasAttribute('data-transition') && item.getAttribute('data-transition') === 'true' ? setIsLeaving(true) : navigateRef.current(path);


        		to.current = path;
        		anchorRef.current = anchor;
        		behaviorRef.current = item.hasAttribute('data-behavior') ? item.getAttribute('data-behavior') : 'auto';


        	}


        	item.addEventListener('click', handleClick);
			events.push({element: item, event: handleClick});

        });


        return () => {

        	if(!events.length) return;
			
			events.forEach(({ element, event }) => {
				
				element.removeEventListener('click', event);
				
			});

        }



	}, [pathname]);


	/*
    * isLeaving transition
    */
	useEffect(() => {
		
		if(!isLeaving) return;
        
		
		const tl = gsap.timeline({
			onComplete: () => {
				
				setIsLeaving(false);
				setIsEntering(true);
				
				
				navigateRef.current(to.current);
                
                if(!window.gscroll){
	                window.gscroll.paused(true);
	                window.gscroll.scrollTop(0);
	            }
	            
                ScrollTrigger.refresh();
                ScrollTrigger.getAll().forEach(t => t.kill());
                
				
			}
		});
		
		
		tl
		.to('main', .2, {
			opacity: 0
		});
		
		
		return () => {
			
			tl.kill();
			
		}
		
		
	}, [isLeaving]);




	/*
    * isEntering transition
    */
	useEffect(() => {
		
		if(!isEntering) return;
		
		
		const tl = gsap.timeline({
			onComplete: () => {
				
				setIsEntering(false);

                if(window.gscroll) window.gscroll.paused(false);


                //if(anchorRef.current)
                //	window.gscroll ? window.gscroll.scrollTo(document.getElementById(anchorRef.current), true, 'top top') : document.getElementById(anchorRef.current).scrollIntoView({behavior: 'smooth'});
				

			}
		});
		
		tl
		.to('main', .2, {
			opacity: 1
		});
		
		
		return () => {
			
			tl.kill();
			
		}
		
		
	}, [isEntering]);
	
	
	return(<main>{children}</main>)
	
}
export default Transitor;