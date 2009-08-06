// Copyright (C) 2003-2009 by Object Mentor, Inc. All rights reserved.
// Released under the terms of the CPL Common Public License version 1.0.
package slim;

import static org.junit.Assert.*;

import static util.ListUtility.list;

import org.junit.*;

import slim.testModule.TestSlimProxy;

import fitnesse.slim.SlimServer;
import fitnesse.slim.StatementExecutorInterface;
import fitnesse.slim.converters.BooleanConverter;

public class PhpSlimMethodInvocationTest {
  private StatementExecutorInterface caller;
  private static final String TEST_SLIM = "testSlim";
  private TestSlimProxy testSlim;

  @Before
  public void setUp() throws Exception {
    caller = new PhpStatementExecutor(new PhpBridge());
    caller.create(TEST_SLIM, "TestModule_TestSlim", new Object[0]);
    testSlim = new TestSlimProxy(TEST_SLIM, caller);
  }

  @Test
  public void callNiladicFunction() throws Exception {
    assertEquals("false", testSlim.niladWasCalled());
    caller.call(TEST_SLIM, "nilad");
    assertEquals("true", testSlim.niladWasCalled());
  }

  @Test
  public void throwMethodNotCalledErrorIfNoSuchMethod() throws Exception {
    String response = (String) caller.call(TEST_SLIM, "noSuchMethod");
    assertTrue(response,
      response.indexOf(SlimServer.EXCEPTION_TAG) != -1 &&
        response.indexOf("message:<<NO_METHOD_IN_CLASS noSuchMethod[0] TestModule_TestSlim.>>") != -1);
  }

  @Test
  public void methodReturnsString() throws Exception {
    Object retval = caller.call(TEST_SLIM, "returnString");
    assertEquals("string", retval);
  }

  @Test
  public void methodReturnsInt() throws Exception {
    Object retval = caller.call(TEST_SLIM, "returnInt");
    assertEquals("7", retval);
  }

  @Test
  public void methodReturnsVoid() throws Exception {
    Object retval = caller.call(TEST_SLIM, "nilad");
    // There is no difference in PHP between functions returning null and void.
    // assertEquals(VoidConverter.VOID_TAG, retval);
    assertNull(retval);
  }

  @Test
  public void methodTakesAndReturnsBooleanTrue() throws Exception {
    Object retval = caller.call(TEST_SLIM, "echoBoolean", "True");
    assertEquals(BooleanConverter.TRUE, retval);
  }

  @Test
  public void methodTakesAndReturnsBooleanFalse() throws Exception {
    Object retval = caller.call(TEST_SLIM, "echoBoolean", "False");
    assertEquals(BooleanConverter.FALSE, retval);
  }


  @Test
  public void passOneString() throws Exception {
    caller.call(TEST_SLIM, "oneString", "string");
    assertEquals("string", testSlim.getStringArg());
  }

  @Test
  public void passOneInt() throws Exception {
    caller.call(TEST_SLIM, "oneInt", "42");
    assertEquals(42, testSlim.getIntArg());
  }

  @Test
  public void passOneDouble() throws Exception {
    caller.call(TEST_SLIM, "oneDouble", "3.14159");
    assertEquals(3.14159, testSlim.getDoubleArg(), .000001);
  }

  @Test
  public void passOneDate() throws Exception {
    caller.call(TEST_SLIM, "oneDate", "5-May-2009");
    assertEquals("5-May-2009", testSlim.getDateArg());
  }

  @Test
  public void passOneList() throws Exception {
    caller.call(TEST_SLIM, "oneList", list("one", "two"));
    assertEquals(list("one", "two"), testSlim.getListArg());
  }

  @Test
  public void passManyArgs() throws Exception {
    caller.call(TEST_SLIM, "manyArgs", "1", "2.1", "c");
    assertEquals(1, testSlim.getIntegerObjectArg().intValue());
    assertEquals(2.1, testSlim.getDoubleObjectArg(), .00001);
    assertEquals('c', testSlim.getCharArg());
  }

  @Test
  public void convertLists() throws Exception {
    caller.call(TEST_SLIM, "oneList", "[1 ,2, 3,4, hello Bob]");
    assertEquals(list("1", "2", "3", "4", "hello Bob"), caller.call(TEST_SLIM, "getListArg"));
  }

  @Test
  public void convertArraysOfStrings() throws Exception {
    caller.call(TEST_SLIM, "setStringArray", "[1 ,2, 3,4, hello Bob]");
    assertEquals("[\"1\", \"2\", \"3\", \"4\", \"hello Bob\"]", caller.call(TEST_SLIM, "getStringArray"));
  }

  @Test
  public void convertArraysOfIntegers() throws Exception {
    caller.call(TEST_SLIM, "setIntegerArray", "[1 ,2, 3,4]");
    assertEquals("[1, 2, 3, 4]", caller.call(TEST_SLIM, "getIntegerArrayAsString"));
  }

  @Test
  public void convertArrayOfIntegersThrowsExceptionIfNotInteger() throws Exception {
    Object result = caller.call(TEST_SLIM, "setIntegerArray", "[1 ,2, 3,4, hello]");
    String resultString = (String) result;
    assertTrue(resultString, resultString.indexOf("message:<<CANT_CONVERT_TO_INTEGER_LIST>>") != -1);
  }

  @Test
  public void convertArraysOfBooleans() throws Exception {
    caller.call(TEST_SLIM, "setBooleanArray", "[true ,false, false,true]");
    assertEquals("[true, false, false, true]", caller.call(TEST_SLIM, "getBooleanArray"));
  }

  @Test
  public void convertArraysOfDoubles() throws Exception {
    caller.call(TEST_SLIM, "setDoubleArray", "[1 ,2.2, -3e2,0.04]");
    assertEquals("[1.0, 2.2, -300.0, 0.04]", caller.call(TEST_SLIM, "getDoubleArray"));
  }

  @Test
  public void convertArrayOfDoublesThrowsExceptionIfNotInteger() throws Exception {
    Object result = caller.call(TEST_SLIM, "setDoubleArray", "[1 ,2, 3,4, hello]");
    String resultString = (String) result;
    assertTrue(resultString, resultString.indexOf("message:<<CANT_CONVERT_TO_DOUBLE_LIST>>") != -1);
  }

  @Test
  public void handleReturnNull() throws Exception {
    Object result = caller.call(TEST_SLIM, "nullString");
    assertNull(result);
  }
}
