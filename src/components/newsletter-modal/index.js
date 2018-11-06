import React, {Component} from 'react';
import ReactDOM from 'react-dom';
import Cookies from 'js-cookie';

import Section from '../section';
import Copy from '../copy';
import SignUp from '../signup';
import Close from '../close-btn';
import Axios from 'axios';

class NewsletterModal extends Component {
	constructor(props) {
		super(props);

		// Static image for the modal
		// @TODO Get this from wp - option set by user
		const link = 'https://www.relativemarketing.co.uk/wp-content/uploads/2018/10/relative-marketing-newsletter';

		this.img = {
			x1: `${link}@x1.jpg`,
			x2: `${link}@x2.jpg`,
			x3: `${link}@x1.jpg`
		}

		// Store some default error messages
		this.notice = {
			success: {
				heading: 'Success! You\'re all signed up.',
				message: 'You are now signed up to the Relative newsletter, look out for the latest news in your mailbox.',
				success: true,
			},
			error: {
				heading: 'Oops, something went wrong',
				message: 'Looks like something went wrong, if the error persists please contact us on 01204 493382',
				success: false,
			}
		}

		this.closeModal = this.closeModal.bind(this);
		this.updateSuccess = this.updateSuccess.bind(this);
		this.toggleComplete = this.toggleComplete.bind(this);
		
		this.state = {
			success: null,
			completed: false,
			copy: {
				heading: '',
				paragraph: '',
			},
			img: {
				x1: '',
				x2: '',
				x3: '',
				alt: '',
			},
			popupDelay: 20000,
			notice: {
				succes: {},
				error: {}
			},
			campaignId: '',
		}
	}

	componentWillMount() {
		
		// Only run if we haven't seen the popup before or haven't seen
		// it in the last 30 days and didn't sign up
		if(!Cookies.get('relative-newsletter-modal') ) {
			// Setup content needed for newsletter display
			Axios
				.get('/wp-json/relativemarketing/newsletter/v1/data')
				.then(({data}) => {
						this.setState(() => (data));
						setTimeout(() => {document.getElementById('relative-newsletter-signup').style.display = 'flex'}, this.state.popupDelay);
				});
		}
	}

	updateSuccess() {
		this.setState(() => ({completed: true, success: true}));
	}

	toggleComplete() {
		this.setState(state => ({completed: !state.completed}));
	}

	closeModal() {
		// Keep showing the message every 30 days unless they sign up
		const days = this.state.success ? 3650 : 30;

		Cookies.set('relative-newsletter-modal', '1', {expires: days});

		// Hide the modal and it's container
		document.getElementById('relative-newsletter-signup').style.display = 'none';

		// Unmount everything as we wont need it again
		ReactDOM.unmountComponentAtNode(ReactDOM.findDOMNode(this).parentNode);
	}

	render() {
		const {img} = this.state;
		return (
			<div className="relative-newsletter-modal">
				<Section>
					<img src={img.x1} srcSet={`${img.x1} 1x, ${img.x2} 2x, ${img.x3} 3x`} alt={img.alt}/>
				</Section>
				<Section>
					<Close onClick={this.closeModal} />
					{!this.state.completed && (
						<Copy>
							<h2 className="h3 relative-newsletter-modal__heading">{this.state.copy.heading}</h2>
							<p>{this.state.copy.paragraph}</p>
						</Copy>
					)}
					<SignUp 
						onClose={this.closeModal}
						isSuccess={this.updateSuccess}
						isComplete={this.state.completed}
						toggleComplete={this.toggleComplete}
						notice={this.notice}
						campaignId={this.state.campaignId}
					/>
				</Section>
			</div>
		);
	}
}

export default NewsletterModal;