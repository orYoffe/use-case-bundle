<?php

namespace spec\Lamudi\UseCaseBundle\Processor\Input;

use Lamudi\UseCaseBundle\Processor\Input\InputProcessorInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @mixin \Lamudi\UseCaseBundle\Processor\Input\FormInputProcessor
 */
class FormInputProcessorSpec extends ObjectBehavior
{
    public function let(FormFactoryInterface $formFactory, FormInterface $form)
    {
        $this->beConstructedWith($formFactory);
        $formFactory->create(Argument::cetera())->willReturn($form);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Lamudi\UseCaseBundle\Processor\Input\FormInputProcessor');
    }

    public function it_is_an_input_processor()
    {
        $this->shouldHaveType(InputProcessorInterface::class);
    }

    public function it_throws_an_exception_if_form_name_is_not_specified()
    {
        $request = new \stdClass();
        $this->shouldThrow(\InvalidArgumentException::class)->duringInitializeRequest($request, [], []);
    }

    public function it_uses_form_factory_to_create_form_by_name(FormFactoryInterface $formFactory)
    {
        $request = new FormUseCaseRequest();
        $this->initializeRequest($request, [], ['name' => 'order_form']);

        $formFactory->create('order_form', $request, ['data_class' => FormUseCaseRequest::class])->shouldHaveBeenCalled();
    }

    public function it_uses_the_created_form_to_populate_request_fields(FormFactoryInterface $formFactory, FormInterface $form)
    {
        $request = new \stdClass();
        $formFactory->create('order_form', Argument::cetera())->willReturn($form);

        $input = ['foo' => 'bar', 'baz' => 213];
        $this->initializeRequest($request, $input, ['name' => 'order_form']);

        $form->handleRequest($input)->shouldHaveBeenCalled();
    }

    public function it_dumps_form_data_to_specified_field(FormFactoryInterface $formFactory, FormInterface $form)
    {
        $request = new \stdClass();
        $formFactory->create('order_form')->willReturn($form);

        $input = ['foo' => 'bar', 'baz' => 213];
        $formData = ['foo' => 'bar_', 'baz' => 213312, 'csrf_token' => 'xyz'];
        $form->handleRequest($input)->shouldBeCalled();
        $form->getData()->willReturn($formData);

        $request = $this->initializeRequest($request, $input, ['name' => 'order_form', 'data_field' => 'formData']);

        $form->submit($input)->shouldNotHaveBeenCalled();
        $request->formData->shouldBe($formData);
    }
}

class FormUseCaseRequest {}
