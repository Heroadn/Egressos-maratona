class Hello_test extends TestCase{
    public function test_get_hello(){
        $output = $this->request('GET',['HELLO','get_hello']);
        $expected = '<h2></h2>';

        $this->assertContains($expected,$output);
    }
}