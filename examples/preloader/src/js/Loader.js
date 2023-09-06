'use strict';
import gsap from './gsap/index.js';
import gsapCore from './gsap/gsap-core.js';

export default class Loader{
    
    constructor(){
        
        return new Promise(done => {
            
            /*
            * Fake preloader based on GSAP
            */
            gsap.delayedCall(1.2, () => done());
            
            /*
            * But you can stop faking and preload your assets here 💪
            */
            
        });
        
    }

}