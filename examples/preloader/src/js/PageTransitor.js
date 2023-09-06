'use strict';
import gsap from './gsap/index.js';
import gsapCore from './gsap/gsap-core.js';
import ScrollTrigger from './gsap/ScrollTrigger.js';
import * as Barba from './barba/Barba.js';


export default class PageTransitor{
    
    constructor(gscroll){
        
        this.gscroll = gscroll;
        
        this.init();
        
    }
    
    onStart(data){
        
        const tl = gsap.timeline();
        
        tl
        .to('#preloader', .6, {
            opacity: 0
        });
        
    }
    
    onLeave(data){
            
        const tl = gsap.timeline();

        tl
        .to('#preloader', .4, {
            opacity: 1
        });
        
        return tl;
        
    }
    
    onAfterLeave(){
        
        this.gscroll.paused(true);
        this.gscroll.scrollTop(0);
        ScrollTrigger.refresh();
        ScrollTrigger.getAll().forEach(t => t.kill());
        
    }
    
    onEnter (data){
        
        const tl = gsap.timeline();

        tl
        .to('#preloader', .4, {
            opacity: 0
        });

        return tl;
        
    }
    
    onAfterEnter(){
        
        this.gscroll.paused(false);
        
    }
    
    
    init(){
        barba.init({
            sync: false,
            debug: false,
            cacheIgnore: true,
            cacheFirstPage: false,
            prefetchIgnore: true,
            preventRunning: true,
            transitions: [
                {
                    once: ({next}) => this.onStart(next),
                    leave: async ({current}) => await this.onLeave(current),
                    afterLeave: () => this.onAfterLeave(),
                    enter: ({next}) => this.onEnter(next),
                    afterEnter: () => this.onAfterEnter()
                }
            ]
        });
    }

}