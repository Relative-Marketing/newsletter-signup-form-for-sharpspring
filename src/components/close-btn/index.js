import React from 'react';

const Close = (props) => {
	return <button onClick={props.onClick} className="relative-newsletter-modal__btn relative-newsletter-modal__btn--close">X</button>
};

export default Close;