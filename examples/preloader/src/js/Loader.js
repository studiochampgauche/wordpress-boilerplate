'use strict';
import gsap from './gsap/index.js';
import gsapCore from './gsap/gsap-core.js';

export default class Loader{
    
    constructor(){
        
        return new Promise(done => {
            
            gsap.delayedCall(1.2, () => done());
            
        });
        
    }

}