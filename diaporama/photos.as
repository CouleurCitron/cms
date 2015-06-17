	this._visible = false;
	//trace("load de " + this._name);
	if ((this.getBytesLoaded() == this.getBytesTotal()) and (this.getBytesLoaded() > 100)){
		//trace(this._name + " is loaded");

		aspectRatioVoulu = wVoulu/hVoulu;
		aspectRatioCourant = this._width/this._height;
		if (aspectRatioCourant >= aspectRatioVoulu){ // on travaille sur la hauteur
			reductionRatio = hVoulu/this._height;
		}
		else{ // on travaille sur la largeur
			reductionRatio = wVoulu/this._width;
		}
		this._xscale = 100*reductionRatio;
		this._yscale = 100*reductionRatio;
		
		this.createEmptyMovieClip("mask", this.getNextHighestDepth());
		mask.beginFill(0xFF0000);
		mask.moveTo(0, 0);
		mask.lineTo(wVoulu, 0);
		mask.lineTo(wVoulu, hVoulu);
		mask.lineTo(0, hVoulu);
		mask.lineTo(0, 0);
		mask.endFill();
		mask._xscale = 100/reductionRatio;
		mask._yscale = 100/reductionRatio;
		this.setMask(mask);

		_global.photosChargees++;
		this._visible = true;
	}