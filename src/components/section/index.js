import React, {Component} from 'react';

class Section extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		return (
			<div className="relative-newsletter-modal__section">
				{this.props.children}
			</div>
		);
	}
}

export default Section;