<script setup lang="ts">
import { ref } from 'vue';
import { Head, Link, useForm as useInertiaForm, router } from '@inertiajs/vue3';
import { useForm as useVeeValidateForm } from 'vee-validate';

import * as yup from 'yup';
import axios from 'axios';

import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { IRegistrationForm } from '@/Types';
import { debounce } from 'lodash';

const translations = {
  registerTitle: "Register",
  nameLabel: "Name",
  emailLabel: "Email",
  passwordLabel: "Password",
  confirmPasswordLabel: "Confirm Password",
  nameRequired: "The name field is required.",
  emailRequired: "The email field is required.",
  emailInvalid: "Please enter a valid email address.",
  emailUniqueFail: "Could not validate email. Please try again.", // Fallback
  emailTaken: "This email address is already taken.", // Example specific message for uniqueness
  passwordRequired: "The password field is required.",
  passwordMin: "The password must be at least 8 characters.",
  passwordConfirmationRequired: "The password confirmation is required.",
  passwordsMismatch: "The passwords do not match.",
  // termsLabel: "Terms", // If using terms
  // termsRequired: "You must accept the terms and conditions.",
  // agreeTo: "I agree to the",
  // termsOfServiceLink: "Terms of Service",
  // privacyPolicyLink: "Privacy Policy",
  alreadyRegisteredLink: "Already registered?",
  registerButton: "Register",
};

const inertiaForm = useInertiaForm<Record<keyof IRegistrationForm, string>>({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
});

const simpleEmailRegex = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
const errorInputClasses = 'border-red-500 focus:border-red-500 focus:ring-red-500';

const checkEmailUniquenessDebounced = debounce(async (emailValue: string): Promise<true | string> => {
  if (!emailValue || !simpleEmailRegex.test(emailValue)) {
    return true;
  }
  try {
    const endpoint = route('api.validate.email');
    const response = await axios.post<{ exists: boolean }>(endpoint, { email: emailValue });

    if (response.data.exists) {
      return translations.emailTaken;
    }

    return true;
  } catch (error: any) {
    let errorMessage = translations.emailTaken;

    if (error.response?.status === 422 && error.response.data.errors?.email) {
      errorMessage = error.response.data.errors.email[0];
    } else {
      errorMessage = translations.emailUniqueFail;
    }
    return errorMessage;
  }
}, 500); // ms

// --- VeeValidate Form Validation ---
const registrationSchema = yup.object({
  name: yup.string().required(translations.nameRequired),
  email: yup.string()
    .required(translations.emailRequired)
    .email(translations.emailInvalid)
    .test(
      'is-unique-email',
      translations.emailTaken,
      async (value: string | undefined, context: yup.TestContext)
        : Promise<boolean | yup.ValidationError> => {
        if (!value) {
          return true;
        }
        const validationResult = await checkEmailUniquenessDebounced(value);
        if (typeof validationResult === 'string') {
          return context?.createError({ message: validationResult });
        }
        return true;
      }
    ),
  password: yup.string()
    .required(translations.passwordRequired)
    .min(8, translations.passwordMin),
  password_confirmation: yup.string()
    .required(translations.passwordConfirmationRequired)
    .oneOf([yup.ref('password')], translations.passwordsMismatch),
});

// --- VeeValidate Form Validation ---
const {
    errors: clientErrors,
    handleSubmit,
    setFieldValue,
    resetForm,
    meta: formMeta,
    defineField,
} = useVeeValidateForm<IRegistrationForm>({
  validationSchema: registrationSchema,
  initialValues: {
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  },
});

// Define fields for v-model binding
const [name, nameAttrs] = defineField('name');
const [email, emailAttrs] = defineField('email');
const [password, passwordAttrs] = defineField('password');
const [password_confirmation, passwordConfirmationAttrs] = defineField('password_confirmation');
// const [terms, termsAttrs] = defineField('terms'); // Uncomment if terms are needed

// --- Input Refs for Focusing ---
const nameInputRef = ref<HTMLInputElement | null>(null);
const emailInputRef = ref<HTMLInputElement | null>(null);
const passwordInputRef = ref<HTMLInputElement | null>(null);
const passwordConfirmationInputRef = ref<HTMLInputElement | null>(null);

// --- Form Submission ---
const submit = handleSubmit(
    async (values) => {

    inertiaForm.name = values.name;
    inertiaForm.email = values.email;
    inertiaForm.password = values.password;
    inertiaForm.password_confirmation = values.password_confirmation;

    const targetRoute = route('registrations.store');

    inertiaForm.post(targetRoute, {
      onSuccess: (page) => {
        resetForm();
      },
      onError: (errors) => {
        console.error('[SUBMIT] inertiaForm.post onError. Server errors:', JSON.stringify(errors));
      },
      onFinish: () => {
        setFieldValue('password', '');
        setFieldValue('password_confirmation', '');
        inertiaForm.password = '';
        inertiaForm.password_confirmation = '';
      },
    });

  },
  // On failed client-side validation (optional: for focusing)
  (context) => {
    console.warn('[SUBMIT] VeeValidate handleSubmit failed (client-side validation). Errors:', JSON.stringify(context.errors));
    const firstErrorField = Object.keys(context.errors)[0];
    if (firstErrorField === 'name' && nameInputRef.value) nameInputRef.value.focus();
    else if (firstErrorField === 'email' && emailInputRef.value) emailInputRef.value.focus();
    else if (firstErrorField === 'password' && passwordInputRef.value) passwordInputRef.value.focus();
    else if (firstErrorField === 'password_confirmation' && passwordConfirmationInputRef.value) passwordConfirmationInputRef.value.focus();
  }
);

</script>

<template>
    <Head :title="translations.registerTitle" />

    <AuthenticationCard>
        <template #logo>
            <AuthenticationCardLogo />
        </template>

        <div v-if="$page.props.flash && $page.props.flash.error" class="mb-4 p-4 text-sm text-red-700 bg-red-100 rounded-lg" role="alert">
            {{ $page.props.flash.error }}
        </div>
        <div v-if="$page.props.errors.email">
          {{ $page.props.errors.email }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" :value="translations.nameLabel" />
                <TextInput
                    id="name"
                    ref="nameInputRef"
                    v-model="name"
                    type="text"
                    class="mt-1 block w-full"
                    :class="{ [errorInputClasses]: clientErrors.name || inertiaForm.errors.name }"
                    autofocus
                    autocomplete="name"
                    v-bind="nameAttrs"
                />
                <InputError class="mt-2" :message="clientErrors.name || inertiaForm.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" :value="translations.emailLabel" />
                <TextInput
                    id="email"
                    ref="emailInputRef"
                    v-model="email"
                    type="email"
                    class="mt-1 block w-full"
                    :class="{ [errorInputClasses]: clientErrors.email || inertiaForm.errors.email }"
                    autocomplete="username"
                    v-bind="emailAttrs"
                />
                <InputError class="mt-2" :message="clientErrors.email || inertiaForm.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" :value="translations.passwordLabel" />
                <TextInput
                    id="password"
                    ref="passwordInputRef"
                    v-model="password"
                    type="password"
                    class="mt-1 block w-full"
                    :class="{ [errorInputClasses]: clientErrors.password || inertiaForm.errors.password }"
                    autocomplete="new-password"
                    v-bind="passwordAttrs"
                />
                <InputError class="mt-2" :message="clientErrors.password || inertiaForm.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel for="password_confirmation" :value="translations.confirmPasswordLabel" />
                <TextInput
                    id="password_confirmation"
                    ref="passwordConfirmationInputRef"
                    v-model="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    :class="{ [errorInputClasses]: clientErrors.password_confirmation || inertiaForm.errors.password_confirmation }"
                    autocomplete="new-password"
                    v-bind="passwordConfirmationAttrs"
                />
                <InputError class="mt-2" :message="clientErrors.password_confirmation || inertiaForm.errors.password_confirmation" />
            </div>

            <div class="flex items-center justify-end mt-4">
                <Link :href="route('login')" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ translations.alreadyRegisteredLink }}
                </Link>

                <PrimaryButton class="ms-4" :class="{ 'opacity-25': inertiaForm.processing }"
                :disabled="inertiaForm.processing">
                    {{ translations.registerButton }}
                </PrimaryButton>
            </div>
        </form>
    </AuthenticationCard>
</template>

<style scoped>
/* Add any specific styles for this page here */
</style>
