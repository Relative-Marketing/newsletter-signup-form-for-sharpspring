import React, {Component} from 'react';
import Axios from 'axios';

class Form extends Component {
	
	constructor(props) {
		super(props);

		this.state = {
			name: '',
			email: '',
		}
		
		this.handleSubmit = this.handleSubmit.bind(this);
		this.updateInput = this.updateInput.bind(this);
	}

	// Update state when user types something
	updateInput(event) {
		const {name, value} = event.target;
		this.setState(() => ({[name]: value}));
	}

	handleSubmit(e) {
		e.preventDefault();

		// Attempt to add the user to sharpspring via the relative newsetter endpoint
		Axios
			.get(`/wp-json/relativemarketing/v1/newsletter/email/${this.state.email}/name/${this.state.name}`)
			.then(({data}) => {
				// If the sharpspring returned an error and the error they sent is not
				// because they are already signed up (code 301)
				const isError = data.error.length && data.error[0].code !== 301 ? true : false;

				this.props.didSubmit(isError);
			})
			.catch(() => {
				this.props.didSubmit(true);
			});
	}

	render() {
		return (
			<form className="relative-newsletter-modal__form" onSubmit={this.handleSubmit}>
				<label htmlFor="name" className="relative-newsletter-modal__label">
					<span className="relative-newsletter-modal__label-text">Full Name</span>
					<input type="text" name="name" id="name" value={this.state.name} onChange={this.updateInput} className="relative-newsletter-modal__input"/>
				</label>
				<label htmlFor="email" className="relative-newsletter-modal__label">
					<span className="relative-newsletter-modal__label-text">Email</span>
					<input type="email" name="email" id="email" value={this.state.email} onChange={this.updateInput} className="relative-newsletter-modal__input"/>
				</label>
				<input type="submit" value="Submit" className="relative-newsletter-modal__btn" />
			</form>
		);
	}
}

export default Form;