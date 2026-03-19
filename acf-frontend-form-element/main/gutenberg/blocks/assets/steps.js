import { store, getContext } from '@wordpress/interactivity';

store('frontend-admin/form', {

  state: {
    activeStep: 0
  },

  actions: {

    setStep: (e) => {
              const context = getContext();
              console.log('Setting step to', context.step);
            context.activeStep = context.step
    }

  },

  callbacks: {

    stepActive: () => {
        const context = getContext();
        console.log('Checking if step is active', context);

      return context.activeStep !== context.step;
    }

  }
});