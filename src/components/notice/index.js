import React, {Component} from 'react';

class Notice extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		let button = <button onClick={() => this.props.onBack()} className="relative-newsletter-modal__btn">Back</button>;

		if(this.props.success) {
			button = <button onClick={() => this.props.onClose()} className="relative-newsletter-modal__btn">Close</button>;
		}
		return (
			<div className="relative-marketing-newsletter__notice">
				<h3 className="relative-newsletter-modal__heading">{this.props.heading}</h3>
				<p>{this.props.message}</p>
				{button}
			</div>
		);
	}
}

export default Notice;