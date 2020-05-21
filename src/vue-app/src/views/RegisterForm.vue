<template>

    <div class="waving d-inline-flex justify-content-center align-items-center">

        <vue-headful title="Регистрация | Личный кабинет"/>

        <b-container class="form">

            <vue-good-wizard
                ref="wizard"
                :steps="steps"
                :onNext="nextClicked"
                :onBack="backClicked"

                :nextStepLabel="'Далее'"
                :previousStepLabel="'Назад'"
                :finalStepLabel="'Отправить'"
            >
                <div slot="page1">
                    <div>
                        <h3 class="form-title">Регистрация</h3>
                    </div>

                    <!-- v-slot="{ passes }" -->
                    <validation-observer ref="registration_observer" >
<!--                        <b-form @submit.stop.prevent="passes(submitAccountRegistration)">-->
                            <b-form-row>
                                <!--                        <div class="social-networks-list d-inline-flex justify-content-center">-->
                                <!--                            <FacebookButton class="mr-3"></FacebookButton>-->
                                <!--                            <GoogleButton></GoogleButton>-->
                                <!--                        </div>-->

                                <!--                        <CenteredCaption>-->
                                <!--                            Или-->
                                <!--                        </CenteredCaption>-->

                                <b-alert variant="danger" v-bind:show="graphql_errors.length > 0" v-if="graphql_errors.length > 0" id="register_errors_container">
                                    {{graphql_errors[0].message}}
                                </b-alert>

                                <validation-provider
                                    style="width: 100%;"

                                    name="Имя"
                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon person-lines-fill"
                                            v-model="name"
                                            placeholder="Имя"
                                            name="name"

                                            :state="getValidationState(validationContext)"
                                            aria-describedby="name-feedback"
                                        ></b-form-input>
                                        <b-form-invalid-feedback id="name-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    name="Фамилия"
                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon person-lines-fill"
                                            v-model="surname"
                                            placeholder="Фамилия"

                                            :state="getValidationState(validationContext)"
                                            aria-describedby="surname-feedback"
                                        ></b-form-input>
                                        <b-form-invalid-feedback id="surname-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    name="Отчество"
                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon person-lines-fill"
                                            v-model="midname"
                                            placeholder="Отчество"

                                            :state="getValidationState(validationContext)"
                                            aria-describedby="midname-feedback"
                                        ></b-form-input>
                                        <b-form-invalid-feedback id="midname-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    name="E-mail"
                                    :rules="{ required: true, email: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon envelope"
                                            v-model="email"
                                            placeholder="E-mail"

                                            :state="getValidationState(validationContext)"
                                            aria-describedby="email-feedback"
                                        ></b-form-input>
                                        <b-form-invalid-feedback id="email-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    name="Пароль"
                                    :rules="{ required: true, password: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon lock-fill"
                                            v-model="password"
                                            placeholder="Пароль"
                                            type="password"
                                            ref="password"
                                            name="password"

                                            :state="getValidationState(validationContext)"
                                            aria-describedby="password-feedback"
                                        ></b-form-input>
                                        <b-form-invalid-feedback id="password-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    name="Подтверждение пароля"
                                    :rules="{ required: true, password: true, password_match: password }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon lock-fill"
                                            v-model="password_matching"
                                            placeholder="Подтвердите пароль"
                                            type="password"

                                            :state="getValidationState(validationContext)"
                                            aria-describedby="password_matching-feedback"
                                        ></b-form-input>
                                        <b-form-invalid-feedback id="password_matching-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    name="Мобильный телефон"
                                    :rules="{ required: true, phone: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon phone"
                                            v-model="phone_number"
                                            placeholder="Мобильный телефон"

                                            :state="getValidationState(validationContext)"
                                            aria-describedby="phone_number-feedback"
                                        ></b-form-input>
                                        <b-form-invalid-feedback id="phone_number-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    name="Пол"
                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-select
                                            placeholder="Пол"
                                            v-model="sex_options_selected"
                                            :options="sex_options"

                                            :state="getValidationState(validationContext)"
                                            aria-describedby="sex-feedback"
                                        ></b-form-select>
                                        <b-form-invalid-feedback id="sex-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    name="Должность"
                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon briefcase-fill"
                                            v-model="job_position"
                                            placeholder="Должность"

                                            :state="getValidationState(validationContext)"
                                            aria-describedby="job_position-feedback"
                                        ></b-form-input>
                                        <b-form-invalid-feedback id="job_position-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    name="Место работы"
                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon building"
                                            v-model="job_place"
                                            placeholder="Место работы"

                                            :state="getValidationState(validationContext)"
                                            aria-describedby="job_place-feedback"
                                        ></b-form-input>
                                        <b-form-invalid-feedback id="job_place-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    name="Дата рождения"
                                    :rules="{ required: true, date: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon"
                                            v-model="birthday"
                                            placeholder="Дата рождения"

                                            :state="getValidationState(validationContext)"
                                            aria-describedby="birthday-feedback"
                                        ></b-form-input>

                                        <b-form-invalid-feedback id="birthday-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <!-- TODO проверка валидации у адресов -->
                                <!-- :rules="{ required: true }" -->
                                <validation-provider
                                    style="width: 100%;"

                                    name="Адрес регистрации"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <AddressInput
                                            v-model="registration_address"
                                            placeholder="Адрес регистрации" />
                                        <b-form-invalid-feedback id="registration_address-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <!-- :rules="{ required: true }" -->
                                <validation-provider
                                    style="width: 100%;"

                                    name="Адрес проживания"

                                    v-slot="validationContext"
                                >


                                    <b-form-group>
                                        <!-- @input="getValidationState(validationContext)" -->
                                        <AddressInput
                                            v-model="residence_address"
                                            placeholder="Адрес проживания"


                                            aria-describedby="residence_address-live-feedback" />
                                        <b-form-invalid-feedback id="residence_address-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>






                                <!-- TODO: Разделить иконки стрелочки и иконки у инпута (через 2 фона) -->

                                <validation-provider
                                    style="width: 100%;"

                                    name="Согласие"
                                    :rules="{agreement: true}"
                                    v-slot="validationContext"
                                >
                                    <b-form-checkbox
                                        v-model="status"
                                        class="accept"

                                        :state="getValidationState(validationContext)"
                                        aria-describedby="agreement-live-feedback"
                                    >
                                        Я согласен(-а) на обработку персональных данных в соответствии с п.&nbsp;4 ст.&nbsp;9 Федерального закона от 27.07.2006 №152-ФЗ "О персональных данных"
                                    </b-form-checkbox>
                                    <!-- Можно и не отображать -->
                                    <!--                            <b-form-invalid-feedback id="agreement-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>-->
                                </validation-provider>

<!--                                <b-button type="submit" block :disabled="is_sending_request">Создать аккаунт</b-button>-->

                            </b-form-row>
<!--                        </b-form>-->
                    </validation-observer>

                </div>
                <div slot="page2">
                    <div>
                        <h3 class="form-title">Подтверждение аккаунта</h3>
                    </div>
                    <p>На Ваш e-mail, указанный при регистрации, был отправлен код подтверждения. Введите его в поле ниже.</p>
                    <p>Письмо может доставляться в течение 5 минут.<br>Если письмо не пришло, проверьте папку "Спам".</p>

                    <b-alert :show="step1_back_notification" dismissible variant="warning">
                        Пожалуйста, введите код подтверждения.
                    </b-alert>

                    <b-alert variant="danger" v-bind:show="incorrect_code" v-if="incorrect_code" id="incorrect_code_message">
                        Неверный код подтверждения. Пожалуйста, проверьте введённый код.
                    </b-alert>

                    <validation-provider
                        style="width: 100%;"

                        ref="key_code"
                        :rules="{ required: true }"
                        v-slot="validationContext"
                    >
                        <b-form-group>
                            <b-form-input
                                class="theme icon envelope"
                                placeholder="Ключ подтверждения"
                                v-model="key_code"

                                :state="getValidationState(validationContext)"
                                aria-describedby="crd-feedback"
                            ></b-form-input>
                            <b-form-invalid-feedback id="crd-feedback">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                        </b-form-group>
                    </validation-provider>


                </div>
                <div slot="page3">
                    <div>
                        <h3 class="form-title" id="children_add">Добавление детей</h3>
                    </div>

                    <b-alert :show="step2_back_notification" dismissible variant="warning">
                        Дополнительных действий по изменению регистрационных данных или кода подтверждения не требуется.
                    </b-alert>



                    <validation-observer ref="children_observer" >
                        <!--                        <b-form @submit.stop.prevent="passes(submitAccountRegistration)">-->
                        <b-form-row>

                            <!-- Форма для каждого ребенка -->
                            <div v-for="(item, index) in children" style="width: 100%;">

                                <hr style="width: 100%;">

                                <b-alert variant="danger"
                                         v-bind:show="graphql_errors.length > 0 && graphql_errors[index] != null"
                                         v-if="graphql_errors.length > 0 && graphql_errors[index] != null">
                                    {{graphql_errors[index].message}}
                                </b-alert>


                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon person-lines-fill"
                                            v-model="item.relationship"
                                            placeholder="Степень родства"

                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-relationship-feedback'"
                                        ></b-form-input>
                                        <b-form-invalid-feedback :id="'cld-'+index+'-relationship-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon person-lines-fill"
                                            v-model="item.name"
                                            placeholder="Имя"

                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-name-feedback'"
                                        ></b-form-input>
                                        <b-form-invalid-feedback :id="'cld-'+index+'-name-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon person-lines-fill"
                                            v-model="item.surname"
                                            placeholder="Фамилия"

                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-surname-feedback'"
                                        ></b-form-input>
                                        <b-form-invalid-feedback :id="'cld-'+index+'-surname-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon person-lines-fill"
                                            v-model="item.midname"
                                            placeholder="Отчество"

                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="midname-feedback"
                                        ></b-form-input>
                                        <b-form-invalid-feedback :id="'cld-'+index+'-surname-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: false, email: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group
                                        description="Необязательное поле">
                                        <b-form-input
                                            class="icon envelope"
                                            v-model="item.email"
                                            placeholder="E-mail"

                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-email-feedback'"
                                        ></b-form-input>
                                        <b-form-invalid-feedback :id="'cld-'+index+'-email-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: true, password: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon lock-fill"
                                            v-model="item.password"
                                            placeholder="Пароль"
                                            type="password"
                                            :ref="'cld-'+index+'-password'"
                                            :name="'cld-'+index+'-password'"

                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-password-feedback'"
                                        ></b-form-input>
                                        <b-form-invalid-feedback :id="'cld-'+index+'-password-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: true, password: true, password_match: item.password }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon lock-fill"
                                            v-model="item.password_matching"
                                            placeholder="Подтвердите пароль"
                                            type="password"

                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-password-matching-feedback'"
                                        ></b-form-input>
                                        <b-form-invalid-feedback :id="'cld-'+index+'-password-matching-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: false, phone: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group
                                        description="Необязательное поле">
                                        <b-form-input
                                            class="icon phone"
                                            v-model="item.phone_number"
                                            placeholder="Мобильный телефон"

                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-phone_number-feedback'"
                                        ></b-form-input>
                                        <b-form-invalid-feedback :id="'cld-'+index+'-phone_number-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-select
                                            placeholder="Пол"
                                            v-model="item.sex_options_selected"
                                            :options="sex_options"

                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-sex-feedback'"
                                        ></b-form-select>
                                        <b-form-invalid-feedback :id="'cld-'+index+'-sex-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon building"
                                            v-model="item.study_place"
                                            placeholder="Место учебы"

                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-study_place-feedback'"
                                        ></b-form-input>
                                        <b-form-invalid-feedback :id="'cld-'+index+'-study_place-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <validation-provider
                                    style="width: 100%;"

                                    :rules="{ required: true }"
                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <b-form-input
                                            class="icon briefcase-fill"
                                            v-model="item.job_position"
                                            placeholder="Класс"

                                            :state="getValidationState(validationContext)"
                                            :aria-describedby="'cld-'+index+'-job_position-feedback'"
                                        ></b-form-input>
                                        <b-form-invalid-feedback :id="'cld-'+index+'-job_position-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <!-- TODO проверка валидации у адресов -->
                                <!-- :rules="{ required: true }" -->
                                <validation-provider
                                    style="width: 100%;"

                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <AddressInput
                                            v-model="item.registration_address"
                                            placeholder="Адрес регистрации" />
                                        <b-form-invalid-feedback :id="'cld-'+index+'-job_position-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>

                                <!-- :rules="{ required: true }" -->
                                <validation-provider
                                    style="width: 100%;"

                                    v-slot="validationContext"
                                >
                                    <b-form-group>
                                        <!-- @input="getValidationState(validationContext)" -->
                                        <AddressInput
                                            v-model="item.residence_address"
                                            placeholder="Адрес проживания"

                                            :aria-describedby="'cld-'+index+'-residence_address-feedback'" />
                                        <b-form-invalid-feedback :id="'cld-'+index+'-residence_address-feedback'">{{ validationContext.errors[0] }}</b-form-invalid-feedback>
                                    </b-form-group>
                                </validation-provider>
                            </div>

                            <a href="#" @click="children.push(child_prototype)">Добавить ребенка</a>







                            <!-- TODO: Разделить иконки стрелочки и иконки у инпута (через 2 фона) -->
                            <validation-provider
                                style="width: 100%;"

                                :rules="{agreement: true}"
                                v-slot="validationContext"
                            >
                                <b-form-checkbox
                                    v-model="status"
                                    class="accept"

                                    :state="getValidationState(validationContext)"
                                    aria-describedby="agreement-live-feedback"
                                >
                                    Я согласен(-а) на обработку персональных данных в соответствии с п.&nbsp;4 ст.&nbsp;9 Федерального закона от 27.07.2006 №152-ФЗ "О персональных данных"
                                </b-form-checkbox>
                            </validation-provider>

                            <!--                                <b-button type="submit" block :disabled="is_sending_request">Создать аккаунт</b-button>-->

                        </b-form-row>
                        <!--                        </b-form>-->
                    </validation-observer>





                </div>
                <div slot="page4">
                    <h4>Step 4</h4>
                    <p>This is step 4</p>
                </div>
                <div slot="page5">
                    <h4>Step 5</h4>
                    <p>This is step 5</p>
                </div>
            </vue-good-wizard>

        </b-container>

        <!-- TODO: config -->
        <vue-particles class="particles" color="#dedede"></vue-particles>
        <div class="city-foreground"></div>
    </div>

</template>

<script>
    import CenteredCaption from "../components/CenteredCaption";
    import FacebookButton from "../components/social/FacebookButton";
    import GoogleButton from "../components/social/GoogleButton";
    import AddressInput from "../components/AddressInput";
    import { GoodWizard } from 'vue-good-wizard';

    export default {
        name: "RegisterForm",
        components: {
            AddressInput,
            FacebookButton,
            CenteredCaption,
            GoogleButton,
            'vue-good-wizard': GoodWizard,
        },
        data: function(){

            // Прототип данных о ребенке
            const child_prototype = {
                relationship: null,
                name: null,
                surname: null,
                midname: null,
                sex_options_selected: null,
                residence_address: null,
                study_place: null,
                study_class: null,
                birthday: null,
                registration_address: null,
                email: null,
                phone_number: null,

                password: null,
                password_matching: null
            };

            return {

                // Шаг 1
                status: false,
                sex_options_selected: null,
                sex_options: [
                    { value: null, text: 'Пол', disabled: true },
                    { value: "м", text: 'Мужской' },
                    { value: "ж", text: 'Женский' },
                ],

                name: null,
                surname: null,
                midname: null,
                email: null,
                password: null,
                password_matching: null,
                phone_number: null,
                job_position: null,
                job_place: null,
                birthday: null,

                registration_address: null,
                residence_address: null,

                // Шаг 2
                key_code: null,

                // Шаг 3
                child_prototype: child_prototype,
                children: [
                    child_prototype
                ],


                // Остальное
                is_sending_request: false,
                graphql_errors: [],

                steps: [
                    {
                        label: 'Регистрация',
                        slot: 'page1',
                    },
                    {
                        label: 'Подтверждение',
                        slot: 'page2',
                        options: {
                            backDisabled: true
                        },
                    },
                    {
                        label: 'Добавление детей',
                        slot: 'page3',
                    },
                    {
                        label: 'Выбор объединения',
                        slot: 'page4',
                        options: {
                            // nextDisabled: true, // control whether next is disabled or not
                        },
                    },
                    {
                        label: 'Заявления',
                        slot: 'page5'
                    }
                ],

                step1_back_notification: false,
                step2_back_notification: false,

                incorrect_code: false,

            };
        },

        methods: {

            nextClicked(currentPage) {
                if (currentPage == 0) {
                    this.submitAccountRegistration();
                    return false;
                }

                if(currentPage == 1){
                    this.checkKeyCode();
                    return false;
                }

                return true;
            },

            backClicked(currentPage) {

                if(currentPage == 1)
                    this.step1_back_notification = true;

                if(currentPage == 2)
                    this.step2_back_notification = true;

                // if(currentPage >= 3)
                //     return true; // Можем вернуться назад

                return false;
            },


            getValidationState({ dirty, validated, valid = null }) {
                return dirty || validated ? valid : null;
            },

            /* Шаг 3 */
            async addChildren(){
                const isValid = await this.$refs.key_code.validate();
                if(!isValid.valid) return false;


                let request = `mutation(`;
                /*
                    mutation(
                        $email: Email!,
                        $key_code: String!
                    ) {
                        validateRegistration (
                            email: $email,
                            key_code: $key_code
                        )
                    }
                `;
                 */

                request += "";



                const data = {
                    email:                  this.email,
                    key_code:               this.key_code
                };

                this.is_sending_request = true;
                this.incorrect_code = true;

                const _component = this;

                this.$request(this.$request_endpoint, request, data).then(function(data){
                    _component.is_sending_request = false;
                    _component.$token = data.validateRegistration;
                    _component.$refs.wizard.currentStep++;
                }).catch(function(e){
                    _component.is_sending_request = false;
                    // let errors = e.response.errors;

                    _component.incorrect_code = true;

                    _component.$nextTick(function(){
                        _component.$scrollTo("#incorrect_code_message");
                    });

                });
            },

            /* Шаг 2 */
            async checkKeyCode(){
                const isValid = await this.$refs.key_code.validate();
                if(!isValid.valid) return false;

                const request = `
                    mutation(
                        $email: Email!,
                        $key_code: String!
                    ) {
                        validateRegistration (
                            email: $email,
                            key_code: $key_code
                        )
                    }
                `;

                const data = {
                    email:                  this.email,
                    key_code:               this.key_code
                };

                this.is_sending_request = true;
                this.incorrect_code = false;

                const _component = this;

                this.$request(this.$request_endpoint, request, data).then(function(data){
                    _component.is_sending_request = false;
                    _component.incorrect_code = false;
                    _component.$token = data.validateRegistration;
                    _component.$refs.wizard.currentStep++;
                }).catch(function(e){
                    _component.is_sending_request = false;
                    // let errors = e.response.errors;

                    _component.incorrect_code = true;

                    _component.$nextTick(function(){
                        _component.$scrollTo("#incorrect_code_message");
                    });

                });
            },

            /* Шаг 1 */
            async submitAccountRegistration(){
                const isValid = await this.$refs.registration_observer.validate();
                if(!isValid) return false;

                const request = `
                    mutation(
                        $name: String!,
                        $surname: String!,
                        $midname: String!,
                        $email: Email!,
                        $password: Password!,
                        $phone_number: PhoneNumber!,
                        $sex: Sex!,
                        $job_position: String!,
                        $job_place: String!,
                        $registration_address: String!,
                        $residence_address: String!,
                        $birthday: Date!
                    ) {
                        register (
                            name: $name,
                            surname: $surname,
                            midname: $midname,
                            email: $email,
                            password: $password,
                            phone_number: $phone_number,
                            sex: $sex,
                            job_position: $job_position,
                            job_place: $job_place,
                            registration_address: $registration_address,
                            residence_address: $residence_address,
                            birthday: $birthday
                        )
                    }
                `;

                const data = {
                    name:                   this.name,
                    surname:                this.surname,
                    midname:                this.midname,
                    email:                  this.email,
                    password:               this.password,
                    phone_number:           this.phone_number,
                    sex:                    this.sex_options_selected,
                    job_position:           this.job_position,
                    job_place:              this.job_place,
                    registration_address:   this.registration_address,
                    residence_address:      this.residence_address,
                    birthday:               this.birthday
                };

                this.is_sending_request = true;
                this.graphql_errors = [];

                const _component = this;

                this.$request(this.$request_endpoint, request, data).then(function(data){
                    _component.is_sending_request = false;
                    _component.$refs.wizard.currentStep++;

                }).catch(function(e){
                    _component.is_sending_request = false;
                    let errors = e.response.errors;
                    if(errors != undefined){
                        _component.graphql_errors = errors;
                    }
                    _component.$nextTick(function(){
                        _component.$scrollTo("#register_errors_container");
                    });

                });
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import "./../assets/waving-form.scss";

    .form{
        max-width: 820px;
        width: 100%;
        /*padding: 70px 70px 70px 70px !important;*/
    }

    .social-networks-list, .alert{
        width: 100%;
    }


    .register-account {
        margin: 50px 0 0 0;
    }
    .lost-password{
        margin: 10px 0 0 0;
        font-size: 10pt;
    }

</style>
